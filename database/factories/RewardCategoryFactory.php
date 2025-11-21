<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RewardCategory>
 */
class RewardCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['Dining', 'Accommodation', 'Wellness', 'Experiences', 'Merchandise', 'Travel']);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->sentence(),
            'display_order' => fake()->numberBetween(1, 10),
            'is_active' => fake()->boolean(90),
        ];
    }
}
