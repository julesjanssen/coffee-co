<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Client\Segment;
use App\Models\Scenario;
use App\Models\ScenarioClient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScenarioClient>
 */
class ScenarioClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'player_id' => fake()->numberBetween(1, 4),
            'title' => fake()->company(),
            'segment' => fake()->randomElement(Segment::cases()),
            'settings' => [],
            'sortorder' => fake()->numberBetween(1, 100),
        ];
    }
}
