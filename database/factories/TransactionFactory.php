<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'check_number' => 'CHK'.fake()->unique()->numerify('########'),
            'guest_name' => fake()->name(),
            'department' => fake()->randomElement(['Restaurant', 'Room Service', 'Spa', 'Bar']),
            'transaction_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'booking_reference' => 'BK'.fake()->numerify('######'),
            'hotel_property' => fake()->company().' Hotel',
            'total_amount' => 0,
            'points_earned' => 0,
            'processed_at' => now(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Transaction $transaction) {
            $lineItemsData = [];
            $categories = ['food', 'beverage', 'rooms', 'miscellaneous'];

            foreach ($categories as $category) {
                if (fake()->boolean(70)) {
                    $lineItemsData[] = [
                        'category' => $category,
                        'amount' => fake()->randomFloat(2, 10, 500),
                    ];
                }
            }

            if (empty($lineItemsData)) {
                $lineItemsData[] = [
                    'category' => 'food',
                    'amount' => fake()->randomFloat(2, 50, 300),
                ];
            }

            $transaction->lineItems()->createMany($lineItemsData);

            $total = $transaction->lineItems()->sum('amount');
            $transaction->update([
                'total_amount' => $total,
                'points_earned' => (int) ($total / 10),
            ]);
        });
    }

    public function withLineItems(array $lineItems): static
    {
        return $this->afterCreating(function (\App\Models\Transaction $transaction) use ($lineItems) {
            $transaction->lineItems()->createMany($lineItems);

            $total = $transaction->lineItems()->sum('amount');
            $transaction->update([
                'total_amount' => $total,
                'points_earned' => (int) ($total / 10),
            ]);
        });
    }
}
