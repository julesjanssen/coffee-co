<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Scenario\Status;
use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scenario>
 */
class ScenarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'slug' => fake()->slug(),
            'status' => Status::ACTIVE,
            'settings' => '{}',
        ];
    }
}
