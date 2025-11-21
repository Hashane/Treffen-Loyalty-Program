<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => \App\Models\Member::factory(),
            'notification_type' => fake()->randomElement(\App\Enums\Notifications\NotificationType::cases()),
            'title' => fake()->sentence(3),
            'message' => fake()->paragraph(),
            'channel' => fake()->randomElement(\App\Enums\Notifications\Channel::cases()),
            'status' => fake()->randomElement(\App\Enums\Notifications\Status::cases()),
            'metadata' => ['additional_info' => fake()->word()],
            'scheduled_for' => fake()->boolean(20) ? fake()->dateTimeBetween('now', '+7 days') : null,
            'sent_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-7 days', 'now') : null,
            'read_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-7 days', 'now') : null,
        ];
    }
}
