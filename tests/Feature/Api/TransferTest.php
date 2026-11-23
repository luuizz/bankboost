<?php

namespace Tests\Feature\Api;

use App\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    public function test_transferencia_move_saldo_e_gera_dois_lancamentos(): void
    {
        $origem = $this->actingAsOwner($this->accountWith(50_000, ['name' => 'Ana']));
        $destino = $this->accountWith(1_000, ['name' => 'Bruno']);

        $response = $this->postJson('/api/v1/transfers', [
            'agency' => $destino->agency,
            'number' => $destino->number,
            'amount' => 12_345,
            'description' => 'Aluguel',
        ]);

        $response->assertCreated()
            ->assertHeader('Idempotent-Replayed', 'false')
            ->assertJsonPath('data.method', 'internal')
            ->assertJsonPath('data.amount', 12_345)
            ->assertJsonPath('data.source.name', 'Ana')
            ->assertJsonPath('data.destination.name', 'Bruno');

        $this->assertSame(37_655, $origem->refresh()->balance);
        $this->assertSame(13_345, $destino->refresh()->balance);

        $debito = $origem->transactions()->latest('id')->first();
        $credito = $destino->transactions()->latest('id')->first();

        $this->assertSame('transfer_out', $debito->type->value);
        $this->assertSame(37_655, $debito->balance_after);
        $this->assertSame('Bruno', $debito->counterparty_name);
        $this->assertSame('transfer_in', $credito->type->value);
        $this->assertSame(13_345, $credito->balance_after);
        $this->assertSame('Ana', $credito->counterparty_name);
        $this->assertSame($debito->transfer_id, $credito->transfer_id);
    }

    public function test_saldo_insuficiente_nao_altera_nada(): void
    {
        $origem = $this->actingAsOwner($this->accountWith(1_000));
        $destino = $this->accountWith();

        $this->postJson('/api/v1/transfers', [
            'agency' => $destino->agency,
            'number' => $destino->number,
            'amount' => 1_001,
        ])->assertUnprocessable()->assertJsonPath('code', 'insufficient_funds');

        $this->assertSame(1_000, $origem->refresh()->balance);
        $this->assertSame(0, $destino->refresh()->balance);
        $this->assertSame(0, Transfer::count());
    }

    public function test_nao_transfere_para_a_propria_conta_nem_para_conta_inexistente(): void
    {
        $origem = $this->actingAsOwner($this->accountWith(5_000));

        $this->postJson('/api/v1/transfers', [
            'agency' => $origem->agency,
            'number' => $origem->number,
            'amount' => 100,
        ])->assertUnprocessable()->assertJsonPath('code', 'same_account');

        $this->postJson('/api/v1/transfers', [
            'agency' => '0001',
            'number' => '000000-0',
            'amount' => 100,
        ])->assertNotFound()->assertJsonPath('code', 'destination_not_found');
    }

    public function test_nao_transfere_para_conta_bloqueada(): void
    {
        $this->actingAsOwner($this->accountWith(5_000));
        $destino = $this->accountWith();
        $destino->update(['status' => 'blocked']);

        $this->postJson('/api/v1/transfers', [
            'agency' => $destino->agency,
            'number' => $destino->number,
            'amount' => 100,
        ])->assertUnprocessable()->assertJsonPath('code', 'account_blocked');
    }

    public function test_chave_de_idempotencia_evita_transferencia_duplicada(): void
    {
        $origem = $this->actingAsOwner($this->accountWith(10_000));
        $destino = $this->accountWith();

        $payload = ['agency' => $destino->agency, 'number' => $destino->number, 'amount' => 2_500];
        $headers = ['Idempotency-Key' => 'pedido-123'];

        $primeira = $this->postJson('/api/v1/transfers', $payload, $headers)->assertCreated();
        $segunda = $this->postJson('/api/v1/transfers', $payload, $headers)
            ->assertOk()
            ->assertHeader('Idempotent-Replayed', 'true');

        $this->assertSame($primeira->json('data.id'), $segunda->json('data.id'));
        $this->assertSame(7_500, $origem->refresh()->balance);
        $this->assertSame(1, Transfer::count());

        $this->postJson('/api/v1/transfers', ['amount' => 3_000] + $payload, $headers)
            ->assertConflict()
            ->assertJsonPath('code', 'idempotency_conflict');
    }

    public function test_limite_diario_de_envio(): void
    {
        config(['banking.limits.daily_outgoing' => 10_000]);

        $this->actingAsOwner($this->accountWith(50_000));
        $destino = $this->accountWith();
        $payload = ['agency' => $destino->agency, 'number' => $destino->number];

        $this->postJson('/api/v1/transfers', $payload + ['amount' => 8_000])->assertCreated();
        $this->postJson('/api/v1/transfers', $payload + ['amount' => 2_001])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'daily_limit_exceeded');
        $this->postJson('/api/v1/transfers', $payload + ['amount' => 2_000])->assertCreated();

        $this->travel(1)->days();

        $this->postJson('/api/v1/transfers', $payload + ['amount' => 5_000])->assertCreated();
    }

    public function test_comprovante_so_para_quem_participou(): void
    {
        $origem = $this->actingAsOwner($this->accountWith(10_000));
        $destino = $this->accountWith();

        $id = $this->postJson('/api/v1/transfers', [
            'agency' => $destino->agency,
            'number' => $destino->number,
            'amount' => 1_000,
        ])->json('data.id');

        $this->getJson("/api/v1/transfers/{$id}")->assertOk()->assertJsonPath('data.amount', 1_000);

        $this->actingAsOwner($destino);
        $this->getJson("/api/v1/transfers/{$id}")->assertOk();

        $this->actingAsOwner($this->accountWith());
        $this->getJson("/api/v1/transfers/{$id}")->assertNotFound();

        $this->assertNotNull($origem);
    }
}
