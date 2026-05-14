<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'content' => fake()->paragraphs(4, true),
            'status' => 'draft',
            'template' => 'default',
            'featured_image' => null,
            'meta_title' => $title,
            'meta_description' => fake()->sentence(),
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
