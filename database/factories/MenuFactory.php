<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => fake()->unique()->slug(),
            'location' => fake()->randomElement(['header', 'footer', 'custom']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
