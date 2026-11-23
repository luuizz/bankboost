<?php

namespace App\Http\Requests\Admin;

use App\Enums\PostCategory;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Sem slug informado, gera a partir do título
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: (string) $this->input('title')),
            'featured' => $this->boolean('featured'),
            'remove_cover' => $this->boolean('remove_cover'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Post|null $post */
        $post = $this->route('post');

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:180', Rule::unique('posts', 'slug')->ignore($post)],
            'category' => ['required', Rule::enum(PostCategory::class)],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:60000'],
            'author_name' => ['required', 'string', 'max:80'],
            'featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=800'],
            'remove_cover' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'excerpt' => 'resumo',
            'body' => 'conteúdo',
            'author_name' => 'autor',
            'published_at' => 'data de publicação',
            'cover' => 'capa',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cover.dimensions' => 'A capa precisa ter pelo menos 800 px de largura.',
            'slug.unique' => 'Já existe um post com este endereço.',
        ];
    }
}
