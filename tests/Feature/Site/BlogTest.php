<?php

namespace Tests\Feature\Site;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_publicados_com_destaque(): void
    {
        Post::factory()->create(['title' => 'Artigo em destaque', 'featured' => true]);
        Post::factory()->create(['title' => 'Artigo comum']);
        Post::factory()->draft()->create(['title' => 'Artigo rascunho']);
        Post::factory()->create(['title' => 'Artigo do futuro', 'published_at' => now()->addDay()]);

        $this->get('/blog')
            ->assertOk()
            ->assertSeeInOrder(['Destaque', 'Artigo em destaque', 'Artigo comum'])
            ->assertDontSee('Artigo rascunho')
            ->assertDontSee('Artigo do futuro');
    }

    public function test_filtra_por_categoria_e_busca(): void
    {
        Post::factory()->create(['title' => 'Golpe do boleto falso', 'category' => 'seguranca']);
        Post::factory()->create(['title' => 'Como investir no Tesouro', 'category' => 'investimentos']);

        $this->get('/blog?categoria=seguranca')
            ->assertSee('Golpe do boleto falso')
            ->assertDontSee('Como investir no Tesouro');

        $this->get('/blog?busca=tesouro')
            ->assertSee('Como investir no Tesouro')
            ->assertDontSee('Golpe do boleto falso')
            ->assertSee('1 resultado');
    }

    public function test_post_publicado_renderiza_markdown_com_seguranca(): void
    {
        $post = Post::factory()->create([
            'title' => 'Guia da reserva',
            'body' => "## Quanto guardar\n\nGuarde **3 a 6 meses**.\n\n<script>alert('x')</script>",
        ]);

        $this->get("/blog/{$post->slug}")
            ->assertOk()
            ->assertSee('<h2>Quanto guardar</h2>', false)
            ->assertSee('<strong>3 a 6 meses</strong>', false)
            ->assertDontSee("<script>alert('x')</script>", false);
    }

    public function test_rascunho_nao_abre(): void
    {
        $post = Post::factory()->draft()->create();

        $this->get("/blog/{$post->slug}")->assertNotFound();
    }

    public function test_relacionados_priorizam_a_mesma_categoria(): void
    {
        $post = Post::factory()->create(['category' => 'investimentos']);
        Post::factory()->create(['title' => 'Mesma categoria', 'category' => 'investimentos', 'published_at' => now()->subDays(30)]);
        Post::factory()->count(3)->create(['category' => 'novidades', 'published_at' => now()->subDay()]);

        $this->get("/blog/{$post->slug}")->assertSee('Mesma categoria');
    }
}
