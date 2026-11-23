<?php

namespace Tests\Feature\Site;

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\JobOpening;
use App\Models\Post;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpAndCareersTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajuda_lista_perguntas_ativas_por_categoria(): void
    {
        Faq::create(['category' => 'pix', 'question' => 'Qual o limite do Pix?', 'answer' => 'R$ 5.000 de dia.']);
        Faq::create(['category' => 'pix', 'question' => 'Pergunta inativa', 'answer' => 'x', 'active' => false]);

        $this->get('/ajuda')
            ->assertOk()
            ->assertSee('Pix e transferências')
            ->assertSee('Qual o limite do Pix?')
            ->assertDontSee('Pergunta inativa')
            ->assertDontSee('Conta PJ</span>', false);
    }

    public function test_formulario_de_contato_salva_a_mensagem(): void
    {
        $this->post('/ajuda/contato', [
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'subject' => 'imprensa',
            'message' => 'Gostaria de uma entrevista sobre o lançamento.',
            'website' => '',
        ])->assertRedirect(route('site.help').'#contato')->assertSessionHas('contact_sent');

        $this->assertDatabaseHas('contact_messages', ['email' => 'maria@example.com', 'subject' => 'imprensa', 'read_at' => null]);
    }

    public function test_formulario_valida_e_barra_robos(): void
    {
        $this->post('/ajuda/contato', ['name' => '', 'email' => 'x', 'subject' => 'nada', 'message' => 'oi'])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        $this->post('/ajuda/contato', [
            'name' => 'Robô',
            'email' => 'robo@example.com',
            'subject' => 'outro',
            'message' => 'Mensagem automática qualquer.',
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_carreiras_lista_vagas_ativas_por_time(): void
    {
        JobOpening::create(['title' => 'Dev Laravel', 'team' => 'Engenharia', 'seniority' => 'Pleno', 'description' => 'PHP e testes.']);
        JobOpening::create(['title' => 'Vaga encerrada', 'team' => 'Engenharia', 'seniority' => 'Júnior', 'description' => 'x', 'active' => false]);

        $this->get('/carreiras')
            ->assertOk()
            ->assertSee('Engenharia')
            ->assertSee('Dev Laravel')
            ->assertDontSee('Vaga encerrada');
    }

    public function test_seeder_de_conteudo_pode_rodar_duas_vezes(): void
    {
        $this->seed(SiteContentSeeder::class);
        $this->seed(SiteContentSeeder::class);

        $this->assertSame(8, Post::count());
        $this->assertSame(6, JobOpening::count());
        $this->assertGreaterThan(15, Faq::count());

        $this->get('/blog')->assertSee('Desafio das 52 semanas');
        $this->get('/ajuda')->assertSee('Qual é o limite do Pix?');
    }
}
