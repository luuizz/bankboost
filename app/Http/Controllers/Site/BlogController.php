<?php

namespace App\Http\Controllers\Site;

use App\Enums\PostCategory;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $category = PostCategory::tryFrom((string) $request->query('categoria'));
        $search = trim((string) $request->query('busca'));
        $filtering = $category !== null || $search !== '';

        // O destaque só aparece na primeira página, sem filtros
        $featured = $filtering || $request->integer('page', 1) > 1
            ? null
            : Post::published()->where('featured', true)->latest('published_at')->first();

        $posts = Post::published()
            ->when($category, fn ($query) => $query->where('category', $category))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%");
            }))
            ->when($featured, fn ($query) => $query->whereKeyNot($featured->id))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('site.pages.blog.index', [
            'featured' => $featured,
            'posts' => $posts,
            'categories' => PostCategory::cases(),
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        return view('site.pages.blog.show', [
            'post' => $post,
            'related' => Post::published()
                ->whereKeyNot($post->id)
                ->orderByRaw('category = ? desc', [$post->category->value])
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
