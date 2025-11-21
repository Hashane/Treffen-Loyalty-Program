<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'points_required' => fake()->randomElement([500, 1000, 2000, 5000, 10000]),
            'qar_value' => fake()->randomFloat(2, 10, 500),
            'category_id' => \App\Models\RewardCategory::factory(),
            'tier_requirement_id' => fake()->boolean(30) ? \App\Models\MembershipTier::factory() : null,
            'available_quantity' => fake()->numberBetween(10, 100),
            'is_unlimited' => fake()->boolean(20),
            'image_url' => fake()->imageUrl(),
            'terms_conditions' => fake()->paragraph(),
            'valid_from' => now(),
            'valid_until' => fake()->dateTimeBetween('+30 days', '+1 year'),
            'is_active' => fake()->boolean(90),
        ];
    }
}
