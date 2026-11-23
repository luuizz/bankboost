<?php

namespace Tests\Feature\Panel;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_e_mandado_para_o_login(): void
    {
        $this->get('/painel')->assertRedirect('/entrar');
    }

    public function test_telas_de_entrada(): void
    {
        $this->get('/entrar')->assertInertia(fn (AssertableInertia $page) => $page->component('Auth/Login'));
        $this->get('/abrir-conta')->assertInertia(fn (AssertableInertia $page) => $page->component('Auth/Register'));
    }

    public function test_abrir_conta_pelo_site(): void
    {
        $this->post('/abrir-conta', [
            'name' => 'Carla Dias',
            'email' => 'carla@example.com',
            'cpf' => '529.982.247-25',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
        ])->assertRedirect('/painel')->assertSessionHas('success');

        $this->assertAuthenticated();
        $this->assertNotNull(User::where('email', 'carla@example.com')->first()?->account);
    }

    public function test_login_logout(): void
    {
        $user = User::factory()->withAccount()->create();

        $this->post('/entrar', ['email' => $user->email, 'password' => 'errada'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/entrar', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/painel');
        $this->assertAuthenticatedAs($user);

        $this->post('/sair')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_usuario_logado_nao_ve_login(): void
    {
        $this->actingAs(User::factory()->withAccount()->create())
            ->get('/entrar')
            ->assertRedirect('/painel');
    }
}
