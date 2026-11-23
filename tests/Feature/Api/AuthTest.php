<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastro_abre_conta_e_devolve_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Maria Teste',
            'email' => 'maria@example.com',
            'cpf' => '529.982.247-25',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token', 'account' => ['agency', 'number', 'balance']])
            ->assertJsonPath('account.balance', 0)
            ->assertJsonPath('account.agency', '0001');

        $user = User::where('email', 'maria@example.com')->firstOrFail();
        $this->assertSame('52998224725', $user->cpf);
        $this->assertNotNull($user->account);
        $this->assertMatchesRegularExpression('/^\d{6}-\d$/', $user->account->number);

        $this->withToken($response->json('token'))
            ->getJson('/api/v1/account')
            ->assertOk()
            ->assertJsonPath('data.holder.email', 'maria@example.com');
    }

    public function test_cadastro_rejeita_cpf_invalido_e_duplicado(): void
    {
        User::factory()->create(['cpf' => '52998224725']);

        $payload = [
            'name' => 'Fulano',
            'email' => 'fulano@example.com',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
        ];

        $this->postJson('/api/v1/auth/register', $payload + ['cpf' => '123.456.789-00'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cpf');

        $this->postJson('/api/v1/auth/register', $payload + ['cpf' => '529.982.247-25'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cpf');
    }

    public function test_login_e_logout(): void
    {
        User::factory()->withAccount()->create(['email' => 'joao@example.com']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'errada',
            'device_name' => 'testes',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'password',
            'device_name' => 'testes',
        ])->assertOk()->json('token');

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();

        $this->assertSame(0, PersonalAccessToken::count());
    }

    public function test_rotas_protegidas_exigem_token(): void
    {
        $this->getJson('/api/v1/account')->assertUnauthorized();
        $this->getJson('/api/v1/statement')->assertUnauthorized();
        $this->postJson('/api/v1/transfers')->assertUnauthorized();
    }
}
