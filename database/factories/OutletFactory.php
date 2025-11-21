<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Outlet>
 */
class OutletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' '.fake()->randomElement(['Restaurant', 'Hotel', 'Spa']),
            'outlet_code' => 'OUT'.fake()->unique()->numerify('###'),
            'outlet_type_id' => \App\Models\OutletType::factory(),
            'location' => fake()->city().', Qatar',
            'phone' => '+974'.fake()->numerify('########'),
            'is_active' => fake()->boolean(90),
        ];
    }
}
