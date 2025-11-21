<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OtpVerification>
 */
class OtpVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'redemption_id' => \App\Models\Redemption::factory(),
            'otp_code' => fake()->numerify('######'),
            'phone_or_email' => fake()->boolean() ? fake()->email() : '+974'.fake()->numerify('########'),
            'channel' => fake()->randomElement(\App\Enums\OtpVerifications\Channel::cases()),
            'attempts' => fake()->numberBetween(0, 3),
            'max_attempts' => 3,
            'verified_at' => fake()->boolean(60) ? fake()->dateTimeBetween('-1 hour', 'now') : null,
            'status' => fake()->randomElement(\App\Enums\OtpVerifications\Status::cases()),
            'sent_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ];
    }
}
