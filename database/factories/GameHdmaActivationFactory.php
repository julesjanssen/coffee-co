<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GameHdmaActivation;
use App\Models\GameSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameHdmaActivation>
 */
class GameHdmaActivationFactory extends Factory
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
            'round_id' => fake()->numberBetween(1, 20),
            'activated_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ];
    }
}
