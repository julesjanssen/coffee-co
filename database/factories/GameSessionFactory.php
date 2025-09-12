<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\Status;
use App\Models\GameSession;
use App\Models\Scenario;
use App\Values\GameSessionSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameSession>
 */
class GameSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scenario_group_id' => fake()->bothify('##??'),
            'scenario_id' => Scenario::factory(),
            'title' => fake()->sentence(2),
            'status' => Status::PENDING,
            'current_round_id' => 0,
            'round_status' => RoundStatus::PAUSED,
            'settings' => new GameSessionSettings(),
        ];
    }
}
