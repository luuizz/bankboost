<?php

namespace Tests\Feature\Api;

use App\Services\Banking\Ledger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class StatementTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    private function montarExtrato(): array
    {
        $ledger = app(Ledger::class);
        $minha = $this->accountWith(0, ['name' => 'Ana']);
        $outra = $this->accountWith(0, ['name' => 'Bruno Mercado']);

        $this->travelTo('2026-11-01 10:00');
        $ledger->deposit($minha, 100_000, 'Salário');

        $this->travelTo('2026-11-05 12:00');
        $ledger->transfer($minha->refresh(), $outra, 20_000, description: 'Compras');

        $this->travelTo('2026-11-10 09:00');
        $ledger->withdraw($minha->refresh(), 5_000);

        $this->travelTo('2026-11-15 18:00');
        $ledger->transfer($outra->refresh(), $minha->refresh(), 1_500, description: 'Reembolso');

        $this->travelBack();

        return [$minha->refresh(), $outra->refresh()];
    }

    public function test_lista_do_mais_recente_para_o_mais_antigo_com_resumo(): void
    {
        [$minha] = $this->montarExtrato();
        $this->actingAsOwner($minha);

        $this->getJson('/api/v1/statement')
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data.0.type', 'transfer_in')
            ->assertJsonPath('data.0.counterparty_name', 'Bruno Mercado')
            ->assertJsonPath('data.3.type', 'deposit')
            ->assertJsonPath('summary.credits', 101_500)
            ->assertJsonPath('summary.debits', 25_000)
            ->assertJsonPath('summary.net', 76_500)
            ->assertJsonPath('summary.balance', 76_500)
            ->assertJsonStructure(['data', 'links', 'meta' => ['current_page', 'total'], 'summary']);
    }

    public function test_filtros(): void
    {
        [$minha] = $this->montarExtrato();
        $this->actingAsOwner($minha);

        $this->getJson('/api/v1/statement?direction=debit')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('summary.credits', 0)
            ->assertJsonPath('summary.debits', 25_000);

        $this->getJson('/api/v1/statement?type=deposit,withdrawal')->assertJsonCount(2, 'data');

        $this->getJson('/api/v1/statement?from=2026-11-05&to=2026-11-10')->assertJsonCount(2, 'data');

        $this->getJson('/api/v1/statement?min_amount=5000&max_amount=20000')->assertJsonCount(2, 'data');

        $this->getJson('/api/v1/statement?search=mercado')->assertJsonCount(2, 'data');

        $this->getJson('/api/v1/statement?per_page=1')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 4)
            ->assertJsonPath('meta.last_page', 4);
    }

    public function test_filtros_invalidos(): void
    {
        $this->actingAsOwner($this->accountWith());

        $this->getJson('/api/v1/statement?type=foo')->assertJsonValidationErrors('type.0');
        $this->getJson('/api/v1/statement?from=2026-11-10&to=2026-11-01')->assertJsonValidationErrors('to');
        $this->getJson('/api/v1/statement?min_amount=500&max_amount=100')->assertJsonValidationErrors('max_amount');
        $this->getJson('/api/v1/statement?per_page=500')->assertJsonValidationErrors('per_page');
    }

    public function test_cada_um_ve_so_o_proprio_extrato(): void
    {
        [, $outra] = $this->montarExtrato();
        $this->actingAsOwner($outra);

        $this->getJson('/api/v1/statement')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('summary.balance', 18_500);
    }
}
