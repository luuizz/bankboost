<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\Banking\Ledger;
use App\Support\Money;
use Illuminate\Console\Command;

class AuditBalances extends Command
{
    protected $signature = 'bankboost:audit';

    protected $description = 'Confere se o saldo de cada conta bate com a soma dos lançamentos';

    public function handle(Ledger $ledger): int
    {
        $divergent = 0;

        Account::query()->orderBy('id')->each(function (Account $account) use ($ledger, &$divergent) {
            $computed = $ledger->computedBalance($account);

            if ($computed !== $account->balance) {
                $divergent++;
                $this->error(sprintf(
                    'Conta %s: saldo %s, lançamentos somam %s',
                    $account->number,
                    Money::format($account->balance),
                    Money::format($computed),
                ));
            }
        });

        if ($divergent > 0) {
            $this->error("{$divergent} conta(s) com divergência.");

            return self::FAILURE;
        }

        $this->info('Todos os saldos conferem com os lançamentos.');

        return self::SUCCESS;
    }
}
