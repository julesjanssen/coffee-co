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
            'group_id' => fake()->bothify('##??'),
            'title' => fake()->sentence(3),
            'locale' => 'en',
            'status' => Status::ACTIVE,
            'settings' => '{}',
        ];
    }
}
