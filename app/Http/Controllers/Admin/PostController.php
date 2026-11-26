<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search'));

        $posts = Post::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->orderByRaw('published_at IS NULL DESC')
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'category' => $post->category_meta['label'],
                'featured' => $post->featured,
                'status' => $this->status($post),
                'published_at' => $post->published_at?->toIso8601String(),
                'url' => route('site.blog.show', $post),
            ]);

        return Inertia::render('admin/posts/Index', [
            'posts' => $posts,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/posts/Form', [
            'post' => null,
            'categories' => $this->categories(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = Post::create($this->attributes($request));

        return redirect()->route('admin.posts.edit', $post)->with('success', 'Post criado.');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('admin/posts/Form', [
            'post' => [
                ...$post->only(['id', 'title', 'slug', 'excerpt', 'body', 'author_name', 'featured']),
                'category' => $post->category->value,
                'published_at' => $post->published_at?->format('Y-m-d\TH:i'),
                'cover_url' => $post->cover_url,
                'url' => route('site.blog.show', $post),
                'status' => $this->status($post),
            ],
            'categories' => $this->categories(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update($this->attributes($request, $post));

        return back()->with('success', 'Post atualizado.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_path) {
            Storage::disk('public')->delete($post->cover_path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post excluído.');
    }

    /**
     * Prévia do Markdown, usando a mesma conversão da página pública.
     */
    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate(['body' => ['nullable', 'string', 'max:60000']]);

        return response()->json([
            'html' => (string) (new Post(['body' => $data['body'] ?? '']))->bodyHtml(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(PostRequest $request, ?Post $post = null): array
    {
        $attributes = $request->safe()->except(['cover', 'remove_cover']);

        if ($request->hasFile('cover') || $request->boolean('remove_cover')) {
            if ($post?->cover_path) {
                Storage::disk('public')->delete($post->cover_path);
            }

            $attributes['cover_path'] = $request->hasFile('cover')
                ? $request->file('cover')->storeAs('posts', Str::slug($attributes['slug']).'-'.Str::lower(Str::random(6)).'.'.$request->file('cover')->extension(), 'public')
                : null;
        }

        return $attributes;
    }

    private function status(Post $post): string
    {
        return match (true) {
            $post->published_at === null => 'draft',
            $post->published_at->isFuture() => 'scheduled',
            default => 'published',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function categories(): array
    {
        return array_map(fn (PostCategory $category) => ['value' => $category->value, 'label' => $category->label()], PostCategory::cases());
    }
}
