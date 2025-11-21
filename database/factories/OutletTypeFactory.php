<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OutletType>
 */
class OutletTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Restaurant', 'Hotel', 'Spa', 'Fitness Center', 'Bar & Lounge', 'Conference Center']),
            'description' => fake()->sentence(),
            'is_active' => fake()->boolean(90),
        ];
    }
}
