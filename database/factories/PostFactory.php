<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $excerpt = fake()->sentence(18);

        return [
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'excerpt' => $excerpt,
            'content' => fake()->paragraphs(5, true),
            'status' => 'draft',
            'featured_image' => null,
            'meta_title' => $title,
            'meta_description' => $excerpt,
            'blocks' => [
                ['type' => 'heading', 'content' => $title],
                ['type' => 'paragraph', 'content' => fake()->paragraph()],
            ],
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
