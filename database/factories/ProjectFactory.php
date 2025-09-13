<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Project\Location;
use App\Enums\Project\Status;
use App\Models\GameSession;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_session_id' => GameSession::factory(),
            'request_id' => fake()->numberBetween(1, 1000),
            'client_id' => fake()->numberBetween(1, 100),
            'status' => Status::PENDING,
            'price' => fake()->numberBetween(1000, 10000),
            'failure_chance' => fake()->numberBetween(0, 100),
            'downtime' => fake()->numberBetween(0, 24),
            'location' => Location::AMSTERDAM,
            'settings' => [],
        ];
    }
}
