<?php

namespace Database\Factories;

use App\Models\Plugin;
use Illuminate\Database\Eloquent\Factories\Factory;

class PluginFactory extends Factory
{
    protected $model = Plugin::class;

    public function definition(): array
    {
        $slug = fake()->unique()->slug();

        return [
            'name' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => fake()->name(),
            'description' => fake()->sentence(),
            'path' => 'plugins/'.$slug,
            'is_active' => false,
            'meta' => ['shortcodes' => []],
        ];
    }
}
