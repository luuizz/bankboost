<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_vai_para_o_login(): void
    {
        $this->get('/admin')->assertRedirect('/entrar');
    }

    public function test_cliente_comum_nao_entra(): void
    {
        $this->actingAs(User::factory()->withAccount()->create());

        foreach (['/admin', '/admin/clientes', '/admin/transacoes', '/admin/posts', '/admin/faq', '/admin/vagas', '/admin/mensagens'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_admin_ve_a_visao_geral(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Dashboard')
            ->has('volume', 14)
            ->where('audit.ok', true)
            ->has('stats.customers')
        );
    }

    public function test_login_de_admin_leva_ao_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/entrar', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
    }

    public function test_cadastro_nao_permite_virar_admin(): void
    {
        $this->post('/abrir-conta', [
            'name' => 'Espertinho',
            'email' => 'esperto@example.com',
            'cpf' => '529.982.247-25',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
            'is_admin' => true,
        ]);

        $this->assertFalse(User::where('email', 'esperto@example.com')->firstOrFail()->isAdmin());
    }

    public function test_comando_make_admin(): void
    {
        $user = User::factory()->create(['email' => 'luiz@example.com']);

        $this->artisan('bankboost:make-admin', ['email' => 'luiz@example.com'])->assertSuccessful();
        $this->assertTrue($user->refresh()->isAdmin());

        $this->artisan('bankboost:make-admin', ['email' => 'luiz@example.com', '--revoke' => true])->assertSuccessful();
        $this->assertFalse($user->refresh()->isAdmin());

        $this->artisan('bankboost:make-admin', ['email' => 'ninguem@example.com'])->assertFailed();
    }
}
