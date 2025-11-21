<?php

namespace Database\Factories;

use App\Enums\Referrals\Status;
use App\Models\Member;
use App\Models\PointsLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referrer_member_id' => Member::factory(),
            'referred_member_id' => fake()->boolean(60) ? Member::factory() : null,
            'referral_code' => strtoupper(fake()->unique()->bothify('???###')),
            'referred_email' => fake()->email(),
            'referred_phone' => '+974'.fake()->numerify('########'),
            'bonus_points_awarded' => 20,
            'points_ledger_id' => fake()->boolean(60) ? PointsLedger::factory() : null,
            'status' => fake()->randomElement(Status::cases()),
            'invited_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'completed_at' => fake()->boolean(60) ? fake()->dateTimeBetween('-2 months', 'now') : null,
        ];
    }
}
