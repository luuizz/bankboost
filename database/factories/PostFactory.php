<?php

namespace Database\Factories;

use App\Enums\PostCategory;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(6), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'category' => fake()->randomElement(PostCategory::cases()),
            'excerpt' => fake()->sentence(14),
            'body' => implode("\n\n", fake()->paragraphs(5)),
            'published_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ];
    }

    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }
}
