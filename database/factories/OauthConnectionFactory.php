<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OauthConnection>
 */
class OauthConnectionFactory extends Factory
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
            'provider' => fake()->randomElement(['google', 'facebook']),
            'provider_id' => fake()->numerify('####################'),
            'provider_token' => 'ya29.'.fake()->sha256(),
            'provider_refresh_token' => fake()->sha256(),
            'avatar' => fake()->imageUrl(96, 96, 'people'),
        ];
    }

    public function google(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'google',
            'provider_id' => fake()->numerify('####################'),
            'provider_token' => 'ya29.'.fake()->sha256(),
            'avatar' => 'https://lh3.googleusercontent.com/a/'.fake()->sha1().'=s96-c',
        ]);
    }

    public function facebook(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'facebook',
            'provider_id' => fake()->numerify('################'),
            'provider_token' => 'EAAG'.fake()->sha256(),
            'avatar' => 'https://graph.facebook.com/'.fake()->numerify('################').'/picture',
        ]);
    }
}
