<?php

namespace Tests\Feature\Api;

use App\Models\PixKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithBank;
use Tests\TestCase;

class PixTest extends TestCase
{
    use InteractsWithBank, RefreshDatabase;

    public function test_cadastra_chaves_de_cada_tipo(): void
    {
        $account = $this->actingAsOwner($this->accountWith(0, [
            'email' => 'Ana@Example.com',
            'cpf' => '52998224725',
        ]));

        $this->postJson('/api/v1/pix/keys', ['type' => 'cpf'])
            ->assertCreated()->assertJsonPath('data.key', '52998224725');

        $this->postJson('/api/v1/pix/keys', ['type' => 'email'])
            ->assertCreated()->assertJsonPath('data.key', 'ana@example.com');

        $this->postJson('/api/v1/pix/keys', ['type' => 'phone', 'phone' => '(11) 98765-4321'])
            ->assertCreated()->assertJsonPath('data.key', '+5511987654321');

        $random = $this->postJson('/api/v1/pix/keys', ['type' => 'random'])->assertCreated()->json('data.key');
        $this->assertTrue(str($random)->isUuid());

        $this->getJson('/api/v1/pix/keys')->assertOk()->assertJsonCount(4, 'data');
        $this->assertSame(4, $account->pixKeys()->count());
    }

    public function test_chave_duplicada_e_limite(): void
    {
        $this->actingAsOwner($this->accountWith());

        $this->postJson('/api/v1/pix/keys', ['type' => 'cpf'])->assertCreated();
        $this->postJson('/api/v1/pix/keys', ['type' => 'cpf'])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'pix_key_already_registered');

        $this->postJson('/api/v1/pix/keys', ['type' => 'phone'])->assertJsonValidationErrors('phone');

        config(['banking.pix.max_keys_per_account' => 2]);
        $this->postJson('/api/v1/pix/keys', ['type' => 'random'])->assertCreated();
        $this->postJson('/api/v1/pix/keys', ['type' => 'random'])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'pix_key_limit_reached');
    }

    public function test_consulta_mostra_recebedor_com_documento_mascarado(): void
    {
        $this->actingAsOwner($this->accountWith());
        $recebedor = $this->accountWith(0, ['name' => 'Bruno Lima', 'email' => 'bruno@example.com', 'cpf' => '52998224725']);
        PixKey::factory()->for($recebedor)->create(['type' => 'email', 'key' => 'bruno@example.com']);

        $this->getJson('/api/v1/pix/lookup?key=BRUNO@example.com')
            ->assertOk()
            ->assertJsonPath('data.name', 'Bruno Lima')
            ->assertJsonPath('data.document', '***.982.247-**')
            ->assertJsonMissingPath('data.cpf');

        $this->getJson('/api/v1/pix/lookup?key=naoexiste@example.com')
            ->assertNotFound()
            ->assertJsonPath('code', 'pix_key_not_found');
    }

    public function test_envia_pix_por_chave(): void
    {
        $origem = $this->actingAsOwner($this->accountWith(20_000));
        $recebedor = $this->accountWith(0, ['cpf' => '52998224725']);
        PixKey::factory()->for($recebedor)->create(['type' => 'cpf', 'key' => '52998224725']);

        $this->postJson('/api/v1/pix/transfers', [
            'key' => '529.982.247-25',
            'amount' => 4_590,
            'description' => 'Pizza',
        ], ['Idempotency-Key' => 'pix-1'])
            ->assertCreated()
            ->assertJsonPath('data.method', 'pix')
            ->assertJsonPath('data.pix_key', '52998224725');

        $this->assertSame(15_410, $origem->refresh()->balance);
        $this->assertSame(4_590, $recebedor->refresh()->balance);
        $this->assertSame('pix_out', $origem->transactions()->latest('id')->first()->type->value);
        $this->assertSame('pix_in', $recebedor->transactions()->latest('id')->first()->type->value);
    }

    public function test_remove_apenas_a_propria_chave(): void
    {
        $account = $this->actingAsOwner($this->accountWith());
        $minha = PixKey::factory()->for($account)->create();
        $alheia = PixKey::factory()->create();

        $this->deleteJson("/api/v1/pix/keys/{$alheia->id}")->assertNotFound();
        $this->deleteJson("/api/v1/pix/keys/{$minha->id}")->assertNoContent();

        $this->assertModelMissing($minha);
        $this->assertModelExists($alheia);
    }
}
