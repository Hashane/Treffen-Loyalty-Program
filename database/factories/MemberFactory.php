<?php

namespace Database\Factories;

use App\Enums\Members\IdType;
use App\Enums\Members\PreferredCommunication;
use App\Enums\Members\Status;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_number' => 'M'.fake()->unique()->numerify('######'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'qatar_id_or_passport' => fake()->numerify('############'),
            'id_type' => fake()->randomElement(IdType::cases()),
            'date_of_birth' => fake()->dateTimeBetween('-65 years', '-18 years'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+974'.fake()->numerify('########'),
            'preferred_communication' => fake()->randomElement(PreferredCommunication::cases()),
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => fake()->boolean(80) ? now() : null,
            'failed_login_attempts' => 0,
            'qr_code_data' => fake()->uuid(),
            'membership_tier_id' => MembershipTier::factory(),
            'current_points' => fake()->numberBetween(0, 5000),
            'lifetime_points' => fake()->numberBetween(100, 10000),
            'referral_code' => strtoupper(fake()->unique()->bothify('???###')),
            'status' => fake()->randomElement(Status::cases()),
            'enrolled_date' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
