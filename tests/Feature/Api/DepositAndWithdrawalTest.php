<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class DepositAndWithdrawalTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    public function test_deposito_credita_a_conta(): void
    {
        $account = $this->actingAsOwner($this->accountWith());

        $this->postJson('/api/v1/deposits', ['amount' => 15_025, 'description' => 'Boleto'])
            ->assertCreated()
            ->assertJsonPath('data.type', 'deposit')
            ->assertJsonPath('data.direction', 'credit')
            ->assertJsonPath('data.balance_after', 15_025)
            ->assertJsonPath('data.amount_formatted', 'R$ 150,25');

        $this->assertSame(15_025, $account->refresh()->balance);
    }

    public function test_saque_debita_e_respeita_saldo(): void
    {
        $account = $this->actingAsOwner($this->accountWith(10_000));

        $this->postJson('/api/v1/withdrawals', ['amount' => 4_000])
            ->assertCreated()
            ->assertJsonPath('data.balance_after', 6_000)
            ->assertJsonPath('data.amount_formatted', '-R$ 40,00');

        $this->postJson('/api/v1/withdrawals', ['amount' => 6_001])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'insufficient_funds');

        $this->assertSame(6_000, $account->refresh()->balance);
        $this->assertSame(2, $account->transactions()->count());
    }

    public function test_valores_invalidos_sao_recusados(): void
    {
        $this->actingAsOwner($this->accountWith());

        $this->postJson('/api/v1/deposits', ['amount' => 0])->assertJsonValidationErrors('amount');
        $this->postJson('/api/v1/deposits', ['amount' => 10.5])->assertJsonValidationErrors('amount');
        $this->postJson('/api/v1/deposits', ['amount' => 10_000_001])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'amount_out_of_range');
    }

    public function test_conta_bloqueada_nao_movimenta(): void
    {
        $account = $this->actingAsOwner($this->accountWith(5_000));
        $account->update(['status' => 'blocked']);

        $this->postJson('/api/v1/withdrawals', ['amount' => 100])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'account_blocked');
    }
}
