<?php

namespace Tests\Feature\Panel;

use App\Models\Account;
use App\Models\PixKey;
use App\Models\Transfer;
use App\Services\Banking\Ledger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    private function loggedIn(int $balance = 0, array $attributes = []): Account
    {
        $account = $this->accountWith($balance, $attributes);
        $this->actingAs($account->user);

        return $account;
    }

    public function test_inicio_mostra_saldo_e_ultimas_movimentacoes(): void
    {
        $this->loggedIn(12_345);

        $this->get('/painel')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->where('account.balance', 12_345)
            ->where('account.balance_formatted', 'R$ 123,45')
            ->has('recent', 1)
            ->where('month.credits', 12_345)
        );
    }

    public function test_deposito_e_saque_pelo_painel(): void
    {
        $account = $this->loggedIn();

        $this->from('/painel')->post('/painel/depositos', ['amount' => 5_000])
            ->assertRedirect('/painel')
            ->assertSessionHas('success', 'Depósito de R$ 50,00 realizado.');

        $this->from('/painel')->post('/painel/saques', ['amount' => 6_000])
            ->assertRedirect('/painel')
            ->assertSessionHasErrors(['amount' => 'Saldo insuficiente para esta operação.']);

        $this->assertSame(5_000, $account->refresh()->balance);
    }

    public function test_transferencia_pelo_painel_leva_ao_comprovante(): void
    {
        $account = $this->loggedIn(10_000);
        $destino = $this->accountWith();

        $response = $this->post('/painel/transferir', [
            'agency' => $destino->agency,
            'number' => $destino->number,
            'amount' => 2_500,
        ], ['Idempotency-Key' => 'painel-1']);

        $transfer = Transfer::firstOrFail();
        $response->assertRedirect("/painel/comprovantes/{$transfer->uuid}");

        // Repetir o envio (duplo clique) não debita de novo
        $this->post('/painel/transferir', [
            'agency' => $destino->agency,
            'number' => $destino->number,
            'amount' => 2_500,
        ], ['Idempotency-Key' => 'painel-1'])->assertRedirect("/painel/comprovantes/{$transfer->uuid}");

        $this->assertSame(7_500, $account->refresh()->balance);

        $this->get("/painel/comprovantes/{$transfer->uuid}")->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Receipt')
            ->where('sent', true)
            ->where('transfer.amount', 2_500)
        );
    }

    public function test_conta_de_destino_inexistente_volta_com_erro(): void
    {
        $this->loggedIn(10_000);

        $this->from('/painel/transferir')->post('/painel/transferir', [
            'agency' => '0001',
            'number' => '000000-0',
            'amount' => 100,
        ])->assertRedirect('/painel/transferir')->assertSessionHasErrors('destination');
    }

    public function test_comprovante_de_terceiros_nao_aparece(): void
    {
        $outra = $this->accountWith(5_000);
        $terceira = $this->accountWith();
        $transfer = app(Ledger::class)->transfer($outra, $terceira, 1_000);

        $this->loggedIn();

        $this->get("/painel/comprovantes/{$transfer->uuid}")->assertNotFound();
    }

    public function test_pix_pelo_painel(): void
    {
        $account = $this->loggedIn(20_000);
        $recebedor = $this->accountWith(0, ['name' => 'Bruno Lima', 'cpf' => '52998224725']);
        PixKey::factory()->for($recebedor)->create(['type' => 'cpf', 'key' => '52998224725']);

        $this->get('/painel/pix')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Pix')
            ->has('keys', 0)
            ->has('keyTypes', 4)
        );

        $this->getJson('/painel/pix/consulta?key=529.982.247-25')
            ->assertOk()
            ->assertJsonPath('data.name', 'Bruno Lima');

        $this->getJson('/painel/pix/consulta?key=nada@example.com')->assertNotFound();

        $this->post('/painel/pix/enviar', ['key' => '52998224725', 'amount' => 3_000])
            ->assertRedirect()
            ->assertSessionHas('success', 'Pix enviado.');

        $this->assertSame(17_000, $account->refresh()->balance);
        $this->assertSame(3_000, $recebedor->refresh()->balance);
    }

    public function test_chaves_pix_pelo_painel(): void
    {
        $account = $this->loggedIn();

        $this->from('/painel/pix')->post('/painel/pix/chaves', ['type' => 'random'])
            ->assertRedirect('/painel/pix')
            ->assertSessionHas('success');

        $key = $account->pixKeys()->firstOrFail();

        $this->from('/painel/pix')->delete("/painel/pix/chaves/{$key->id}")->assertRedirect('/painel/pix');
        $this->assertModelMissing($key);
    }

    public function test_extrato_com_filtros(): void
    {
        $account = $this->loggedIn(10_000);
        app(Ledger::class)->withdraw($account, 1_000, 'Feira');

        $this->get('/painel/extrato?direction=debit&type[]=withdrawal')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Statement')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.description', 'Feira')
            ->where('summary.debits', 1_000)
            ->where('filters.direction', 'debit')
            ->has('types', 6)
        );
    }
}
