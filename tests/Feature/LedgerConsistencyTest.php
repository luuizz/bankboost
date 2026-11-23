<?php

namespace Tests\Feature;

use App\Exceptions\Banking\BankingException;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\Banking\Ledger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class LedgerConsistencyTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    public function test_saldos_batem_com_os_lancamentos_depois_de_muitas_operacoes(): void
    {
        config(['banking.limits.daily_outgoing' => PHP_INT_MAX]);

        $ledger = app(Ledger::class);
        $accounts = collect(range(1, 5))->map(fn () => $this->accountWith(50_000));
        $deposited = 250_000;
        $withdrawn = 0;

        mt_srand(42);

        for ($i = 0; $i < 200; $i++) {
            $from = $accounts->random()->refresh();
            $amount = mt_rand(1, 30_000);

            try {
                switch (mt_rand(1, 4)) {
                    case 1:
                        $ledger->deposit($from, $amount);
                        $deposited += $amount;
                        break;
                    case 2:
                        $ledger->withdraw($from, $amount);
                        $withdrawn += $amount;
                        break;
                    default:
                        $to = $accounts->reject(fn (Account $account) => $account->is($from))->random()->refresh();
                        $ledger->transfer($from, $to, $amount);
                }
            } catch (BankingException) {
                // Saldo insuficiente faz parte do sorteio
            }
        }

        $this->artisan('bankboost:audit')->assertSuccessful();

        $this->assertSame($deposited - $withdrawn, (int) Account::sum('balance'));

        $this->assertSame(0, Account::where('balance', '<', 0)->count());
    }

    public function test_auditoria_acusa_divergencia(): void
    {
        $account = $this->accountWith(1_000);
        Account::whereKey($account->id)->update(['balance' => 999]);

        $this->artisan('bankboost:audit')->assertFailed();
    }

    public function test_lancamentos_sao_imutaveis(): void
    {
        $transaction = $this->accountWith(1_000)->transactions()->firstOrFail();

        $this->expectException(LogicException::class);

        $transaction->update(['amount' => 1]);
    }

    public function test_extrato_registra_saldo_apos_cada_lancamento(): void
    {
        $account = $this->accountWith();
        $ledger = app(Ledger::class);

        $ledger->deposit($account, 1_000);
        $ledger->withdraw($account, 300);
        $ledger->deposit($account, 50);

        $this->assertSame(
            [1_000, 700, 750],
            Transaction::where('account_id', $account->id)->orderBy('id')->pluck('balance_after')->all(),
        );
    }
}
