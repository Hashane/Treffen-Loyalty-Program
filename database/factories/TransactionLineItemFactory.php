<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionLineItem>
 */
class TransactionLineItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'food',
            'beverage',
            'rooms',
            'laundry',
            'telephone',
            'dry_cleaning',
            'service_charge',
            'washing',
            'miscellaneous',
        ];

        return [
            'category' => fake()->randomElement($categories),
            'amount' => fake()->randomFloat(2, 10, 500),
        ];
    }

    public function food(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'food',
            'amount' => fake()->randomFloat(2, 20, 200),
        ]);
    }

    public function beverage(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'beverage',
            'amount' => fake()->randomFloat(2, 10, 100),
        ]);
    }

    public function rooms(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'rooms',
            'amount' => fake()->randomFloat(2, 100, 1000),
        ]);
    }
}
