<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointsLedger>
 */
class PointsLedgerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pointsChange = fake()->numberBetween(-500, 500);
        $currentPointsAfter = fake()->numberBetween(0, 5000);
        $lifetimePointsAfter = fake()->numberBetween(100, 10000);

        return [
            'member_id' => \App\Models\Member::factory(),
            'points_change' => $pointsChange,
            'points_type' => fake()->randomElement(\App\Enums\PointsLedger\PointsType::cases()),
            'current_points_after' => $currentPointsAfter,
            'lifetime_points_after' => $lifetimePointsAfter,
            'expiry_date' => fake()->boolean(30) ? fake()->dateTimeBetween('+30 days', '+1 year') : null,
            'expired' => false,
            'description' => fake()->sentence(),
        ];
    }
}
