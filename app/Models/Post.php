<?php

namespace App\Models;

use App\Enums\PostCategory;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'category', 'excerpt', 'body', 'cover_path', 'author_name', 'featured', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Corpo em Markdown convertido para HTML. HTML cru digitado no admin é escapado.
     */
    public function bodyHtml(): HtmlString
    {
        return new HtmlString(Str::markdown($this->body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]));
    }

    /**
     * @return Attribute<array{label: string, icon: string, gradient: string}, never>
     */
    protected function categoryMeta(): Attribute
    {
        return Attribute::get(fn () => $this->category->meta());
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn () => $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null);
    }

    /**
     * @return Attribute<int, never>
     */
    protected function readingMinutes(): Attribute
    {
        return Attribute::get(fn () => max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200)));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function publishedLabel(): Attribute
    {
        return Attribute::get(fn () => $this->published_at?->translatedFormat('j \d\e F \d\e Y') ?? 'Rascunho');
    }

    protected function casts(): array
    {
        return [
            'category' => PostCategory::class,
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
