<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\Banking\Ledger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class CustomersTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_lista_e_busca_clientes(): void
    {
        $this->accountWith(0, ['name' => 'Carla Dias', 'cpf' => '52998224725']);
        $this->accountWith(0, ['name' => 'Bruno Lima']);

        $this->get('/admin/clientes')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/customers/Index')
            ->has('accounts.data', 2)
        );

        $this->get('/admin/clientes?search=529.982')->assertInertia(fn (AssertableInertia $page) => $page
            ->has('accounts.data', 1)
            ->where('accounts.data.0.holder.name', 'Carla Dias')
            ->where('accounts.data.0.holder.cpf', '529.982.247-25')
        );

        $this->get('/admin/clientes?search=Bruno+Lima')->assertInertia(fn (AssertableInertia $page) => $page
            ->has('accounts.data', 1)
            ->where('accounts.data.0.holder.name', 'Bruno Lima')
        );
    }

    public function test_detalhe_mostra_saldo_e_conferencia(): void
    {
        $account = $this->accountWith(10_000);

        $this->get("/admin/clientes/{$account->id}")->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/customers/Show')
            ->where('account.balance', 10_000)
            ->where('account.computed_balance', 10_000)
            ->has('transactions.data', 1)
        );
    }

    public function test_bloquear_impede_movimentar_e_desbloquear_libera(): void
    {
        $account = $this->accountWith(10_000);

        $this->post("/admin/clientes/{$account->id}/bloquear", ['reason' => 'x'])->assertSessionHasErrors('reason');

        $this->post("/admin/clientes/{$account->id}/bloquear", ['reason' => 'Suspeita de fraude'])
            ->assertSessionHas('success');

        $account->refresh();
        $this->assertFalse($account->isActive());
        $this->assertSame('Suspeita de fraude', $account->blocked_reason);
        $this->assertSame(10_000, $account->balance);

        Sanctum::actingAs($account->user);
        $this->postJson('/api/v1/withdrawals', ['amount' => 100])->assertJsonPath('code', 'account_blocked');

        $this->actingAs(User::factory()->admin()->create());
        $this->post("/admin/clientes/{$account->id}/desbloquear")->assertSessionHas('success');

        $account->refresh();
        $this->assertTrue($account->isActive());
        $this->assertNull($account->blocked_reason);

        app(Ledger::class)->withdraw($account, 100);
        $this->assertSame(9_900, $account->refresh()->balance);
    }

    public function test_transacoes_de_todo_o_banco_com_filtro(): void
    {
        $this->accountWith(5_000);
        $other = $this->accountWith(3_000);
        app(Ledger::class)->withdraw($other, 1_000, 'Feira');

        $this->get('/admin/transacoes')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Transactions')
            ->has('transactions.data', 3)
        );

        $this->get('/admin/transacoes?type[]=withdrawal')->assertInertia(fn (AssertableInertia $page) => $page
            ->has('transactions.data', 1)
            ->where('transactions.data.0.description', 'Feira')
            ->where('transactions.data.0.account.number', $other->number)
        );
    }
}
