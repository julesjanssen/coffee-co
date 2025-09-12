<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GameSession\ScoreType;
use App\Models\GameScore;
use App\Models\GameSession;
use App\Models\ScenarioClient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameScore>
 */
class GameScoreFactory extends Factory
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
            'participant_id' => null,
            'client_id' => ScenarioClient::factory(),
            'type' => ScoreType::collect()->random(),
            'trigger_type' => 'manual',
            'trigger_id' => null,
            'event' => 'test',
            'details' => [],
            'round_id' => fake()->numberBetween(1, 20),
            'value' => fake()->numberBetween(-50, 50),
        ];
    }
}
