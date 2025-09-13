<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Participant\Role;
use App\Models\GameParticipant;
use App\Models\GameSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameParticipant>
 */
class GameParticipantFactory extends Factory
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
            'role' => Role::SALES_1,
        ];
    }
}
