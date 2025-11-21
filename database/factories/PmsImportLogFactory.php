<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PmsImportLog>
 */
class PmsImportLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $recordsProcessed = fake()->numberBetween(100, 1000);
        $recordsSuccessful = fake()->numberBetween(80, $recordsProcessed);
        $recordsFailed = fake()->numberBetween(0, 20);
        $recordsDuplicate = $recordsProcessed - $recordsSuccessful - $recordsFailed;

        return [
            'import_type' => fake()->randomElement(\App\Enums\PmsImportLogs\ImportType::cases()),
            'file_name' => 'import_'.fake()->dateTime()->format('Y-m-d_His').'.csv',
            'file_size_kb' => fake()->numberBetween(10, 5000),
            'records_processed' => $recordsProcessed,
            'records_successful' => $recordsSuccessful,
            'records_failed' => $recordsFailed,
            'records_duplicate' => $recordsDuplicate,
            'error_details' => ['errors' => []],
            'summary' => ['total' => $recordsProcessed],
            'status' => fake()->randomElement(\App\Enums\PmsImportLogs\Status::cases()),
            'imported_by' => \App\Models\User::factory(),
            'started_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
