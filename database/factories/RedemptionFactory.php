<?php

namespace Database\Factories;

use App\Enums\Redemptions\Status;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Redemption>
 */
class RedemptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pointsUsed = fake()->randomElement([500, 1000, 2000, 5000]);

        return [
            'member_id' => Member::factory(),
            'reward_id' => Reward::factory(),
            'outlet_id' => fake()->boolean(80) ? Outlet::factory() : null,
            'staff_user_id' => fake()->boolean(80) ? User::factory() : null,
            'points_used' => $pointsUsed,
            'qar_amount' => $pointsUsed / 10,
            'redemption_code' => 'RDM'.fake()->unique()->numerify('########'),
            'status' => fake()->randomElement(Status::cases()),
            'initiated_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'completed_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
