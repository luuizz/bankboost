<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\JobOpening;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Conteúdo inicial do site: artigos do blog, perguntas frequentes e vagas.
 *
 * Roda a cada deploy, mas só preenche tabelas vazias: o que for editado
 * no admin nunca é sobrescrito.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Post::query()->doesntExist()) {
            foreach (require __DIR__.'/content/posts.php' as $post) {
                Post::create([
                    'title' => $post['title'],
                    'slug' => Str::slug($post['title']),
                    'category' => $post['category'],
                    'excerpt' => $post['excerpt'],
                    'body' => $post['body'],
                    'featured' => $post['featured'] ?? false,
                    'published_at' => now()->subDays($post['days_ago'])->setTime(9, 0),
                ]);
            }
        }

        if (Faq::query()->doesntExist()) {
            foreach (require __DIR__.'/content/faqs.php' as $category => $items) {
                foreach ($items as $position => [$question, $answer]) {
                    Faq::create([
                        'category' => $category,
                        'question' => $question,
                        'answer' => $answer,
                        'position' => $position,
                    ]);
                }
            }
        }

        if (JobOpening::query()->doesntExist()) {
            foreach (require __DIR__.'/content/jobs.php' as [$title, $team, $seniority, $description]) {
                JobOpening::create([
                    'title' => $title,
                    'team' => $team,
                    'seniority' => $seniority,
                    'location' => 'Remoto (Brasil)',
                    'contract' => 'CLT',
                    'description' => $description,
                ]);
            }
        }
    }
}
