<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipTier>
 */
class MembershipTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $level = 0;
        $level++;

        $tierLevel = min($level, 5);

        return [
            'tier_name' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']),
            'tier_level' => $tierLevel,
            'points_threshold' => $tierLevel * 1000,
            'points_multiplier' => 1 + ($tierLevel * 0.25),
            'benefits' => [
                'discount_percentage' => $tierLevel * 5,
                'priority_support' => $tierLevel >= 3,
                'exclusive_events' => $tierLevel >= 4,
            ],
            'is_active' => true,
        ];
    }
}
