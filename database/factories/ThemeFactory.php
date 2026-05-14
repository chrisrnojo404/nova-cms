<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThemeFactory extends Factory
{
    protected $model = Theme::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(2, true)),
            'slug' => fake()->unique()->slug(),
            'version' => '1.0.0',
            'author' => fake()->name(),
            'description' => fake()->sentence(),
            'path' => 'themes/'.fake()->unique()->slug(),
            'is_active' => false,
            'meta' => ['type' => 'frontend'],
        ];
    }
}
