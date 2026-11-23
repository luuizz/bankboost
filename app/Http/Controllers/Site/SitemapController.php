<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $pages = collect([
            'home', 'site.account', 'site.card', 'site.investments', 'site.loans', 'site.business',
            'site.about', 'site.careers', 'site.blog.index', 'site.help', 'site.terms', 'site.privacy',
        ])->map(fn (string $name) => ['loc' => route($name), 'lastmod' => null]);

        $posts = Post::published()->latest('published_at')->get(['slug', 'updated_at'])
            ->map(fn (Post $post) => ['loc' => route('site.blog.show', $post), 'lastmod' => $post->updated_at?->toAtomString()]);

        return response()
            ->view('site.sitemap', ['urls' => $pages->concat($posts)])
            ->header('Content-Type', 'application/xml');
    }
}
