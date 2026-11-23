<?php

namespace Tests\Feature\Site;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        return [
            'home' => ['/', 'Desperte seu potencial financeiro'],
            'conta digital' => ['/conta-digital', 'Uma conta sem tarifa que trabalha por você'],
            'cartão' => ['/cartao', 'Zero anuidade, cashback e controle'],
            'investimentos' => ['/investimentos', 'Quanto o seu dinheiro pode render?'],
            'empréstimo' => ['/emprestimo', 'Simule sem compromisso'],
            'conta pj' => ['/conta-pj', 'A conta que faz o seu negócio decolar'],
            'quem somos' => ['/quem-somos', 'A gente existe para descomplicar'],
            'carreiras' => ['/carreiras', 'Venha construir o banco'],
            'ajuda' => ['/ajuda', 'Como a gente pode te ajudar hoje?'],
            'blog' => ['/blog', 'Conteúdo para cuidar bem do seu dinheiro'],
            'termos' => ['/termos', 'Termos de uso'],
            'privacidade' => ['/privacidade', 'Política de privacidade'],
        ];
    }

    #[DataProvider('pages')]
    public function test_paginas_institucionais_carregam(string $url, string $text): void
    {
        $this->get($url)
            ->assertOk()
            ->assertSee($text)
            ->assertSee(route('register'), false);
    }

    public function test_home_mostra_os_ultimos_posts_publicados(): void
    {
        Post::factory()->create(['title' => 'Post publicado na home']);
        Post::factory()->draft()->create(['title' => 'Rascunho escondido']);

        $this->get('/')
            ->assertSee('Post publicado na home')
            ->assertDontSee('Rascunho escondido');
    }

    public function test_sitemap_lista_paginas_e_posts(): void
    {
        $post = Post::factory()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('site.card'), false)
            ->assertSee(route('site.blog.show', $post), false);
    }
}
