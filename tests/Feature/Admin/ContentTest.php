<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\JobOpening;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
        Storage::fake('public');
    }

    private function postPayload(array $overrides = []): array
    {
        return [
            'title' => 'Como montar sua reserva',
            'slug' => '',
            'category' => 'dinheiro',
            'excerpt' => 'Um guia rápido.',
            'body' => "## Passo 1\n\nGuarde todo mês.",
            'author_name' => 'Equipe Bankboost',
            'featured' => false,
            'published_at' => now()->subHour()->format('Y-m-d\TH:i'),
            ...$overrides,
        ];
    }

    public function test_cria_post_com_slug_automatico_e_capa(): void
    {
        $response = $this->post('/admin/posts', $this->postPayload([
            'cover' => UploadedFile::fake()->image('capa.jpg', 1200, 750),
        ]));

        $post = Post::firstOrFail();
        $response->assertRedirect("/admin/posts/{$post->slug}/edit");

        $this->assertSame('como-montar-sua-reserva', $post->slug);
        $this->assertNotNull($post->cover_path);
        Storage::disk('public')->assertExists($post->cover_path);

        $this->get("/blog/{$post->slug}")->assertOk()->assertSee('Como montar sua reserva');
    }

    public function test_valida_slug_unico_e_tamanho_da_capa(): void
    {
        Post::factory()->create(['slug' => 'como-montar-sua-reserva']);

        $this->post('/admin/posts', $this->postPayload([
            'cover' => UploadedFile::fake()->image('pequena.jpg', 300, 200),
        ]))->assertSessionHasErrors(['slug', 'cover']);
    }

    public function test_edita_troca_e_remove_capa(): void
    {
        $this->post('/admin/posts', $this->postPayload(['cover' => UploadedFile::fake()->image('a.jpg', 1200, 750)]));
        $post = Post::firstOrFail();
        $first = $post->cover_path;

        $this->post("/admin/posts/{$post->slug}", $this->postPayload([
            '_method' => 'put',
            'slug' => $post->slug,
            'title' => 'Título novo',
            'cover' => UploadedFile::fake()->image('b.jpg', 1200, 750),
        ]))->assertSessionHas('success');

        $post->refresh();
        $this->assertSame('Título novo', $post->title);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($post->cover_path);

        $this->put("/admin/posts/{$post->slug}", $this->postPayload(['slug' => $post->slug, 'remove_cover' => true]));
        $this->assertNull($post->refresh()->cover_path);
    }

    public function test_rascunho_e_agendado_nao_aparecem_no_site(): void
    {
        $this->post('/admin/posts', $this->postPayload(['title' => 'Rascunho', 'published_at' => '']));
        $this->post('/admin/posts', $this->postPayload(['title' => 'Agendado', 'published_at' => now()->addDay()->format('Y-m-d\TH:i')]));

        $this->get('/blog')->assertDontSee('Rascunho')->assertDontSee('Agendado');

        $this->get('/admin/posts')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/posts/Index')
            ->has('posts.data', 2)
        );
    }

    public function test_previa_do_markdown_escapa_html(): void
    {
        $this->postJson('/admin/posts/preview', ['body' => "**forte** <script>x</script>"])
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, '<strong>forte</strong>') && ! str_contains($html, '<script>'));
    }

    public function test_exclui_post_e_capa(): void
    {
        $this->post('/admin/posts', $this->postPayload(['cover' => UploadedFile::fake()->image('a.jpg', 1200, 750)]));
        $post = Post::firstOrFail();

        $this->delete("/admin/posts/{$post->slug}")->assertRedirect('/admin/posts');

        $this->assertModelMissing($post);
        Storage::disk('public')->assertMissing($post->cover_path);
    }

    public function test_crud_de_faq(): void
    {
        $this->post('/admin/faq', ['category' => 'pix', 'question' => 'Pix cai na hora?', 'answer' => 'Sim.', 'position' => 0, 'active' => true])
            ->assertSessionHas('success');

        $faq = Faq::firstOrFail();
        $this->put("/admin/faq/{$faq->id}", ['category' => 'pix', 'question' => 'Pix cai na hora?', 'answer' => 'Sim, 24/7.', 'position' => 2, 'active' => false]);

        $faq->refresh();
        $this->assertSame('Sim, 24/7.', $faq->answer);
        $this->assertFalse($faq->active);
        $this->get('/ajuda')->assertDontSee('Pix cai na hora?');

        $this->delete("/admin/faq/{$faq->id}");
        $this->assertModelMissing($faq);
    }

    public function test_crud_de_vagas(): void
    {
        $payload = ['title' => 'Dev Vue', 'team' => 'Engenharia', 'location' => 'Remoto', 'seniority' => 'Pleno', 'contract' => 'CLT', 'description' => 'Vue e TS.', 'active' => true];

        $this->post('/admin/vagas', $payload)->assertSessionHas('success');
        $this->post('/admin/vagas', [...$payload, 'seniority' => 'Mago'])->assertSessionHasErrors('seniority');

        $job = JobOpening::firstOrFail();
        $this->get('/carreiras')->assertSee('Dev Vue');

        $this->put("/admin/vagas/{$job->id}", [...$payload, 'active' => false]);
        $this->get('/carreiras')->assertDontSee('Dev Vue');

        $this->delete("/admin/vagas/{$job->id}");
        $this->assertModelMissing($job);
    }

    public function test_mensagens_marca_como_lida_e_exclui(): void
    {
        $message = ContactMessage::create(['name' => 'Ana', 'email' => 'ana@example.com', 'subject' => 'outro', 'message' => 'Olá, tudo bem?']);

        $this->get('/admin/mensagens?nao_lidas=1')->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Messages')
            ->has('messages.data', 1)
        );

        $this->post("/admin/mensagens/{$message->id}/lida");
        $this->assertTrue($message->refresh()->isRead());

        $this->get('/admin/mensagens?nao_lidas=1')->assertInertia(fn (AssertableInertia $page) => $page->has('messages.data', 0));

        $this->delete("/admin/mensagens/{$message->id}");
        $this->assertModelMissing($message);
    }
}
