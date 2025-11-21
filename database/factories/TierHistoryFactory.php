<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TierHistory>
 */
class TierHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'from_tier' => fake()->randomElement(['Bronze', 'Silver', 'Gold']),
            'to_tier' => fake()->randomElement(['Silver', 'Gold', 'Platinum']),
            'lifetime_points_at_upgrade' => fake()->numberBetween(1000, 10000),
            'current_points_at_upgrade' => fake()->numberBetween(100, 5000),
            'upgraded_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
