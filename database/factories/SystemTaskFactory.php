<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SystemTaskStatus;
use App\Models\SystemTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SystemTask>
 */
class SystemTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'test-task',
            'status' => SystemTaskStatus::COMPLETED,
            'output' => 'Task completed successfully',
        ];
    }
}
