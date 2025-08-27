<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GameFacilitator;
use App\Models\GameSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<GameFacilitator>
 */
class GameFacilitatorFactory extends Factory
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
            'name' => fake()->name(),
            'hash' => fake()->bothify('facilitator###'),
            'password' => Hash::make('password'),
        ];
    }
}
