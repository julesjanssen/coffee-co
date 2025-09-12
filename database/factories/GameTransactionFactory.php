<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GameSession\TransactionType;
use App\Models\GameSession;
use App\Models\GameTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameTransaction>
 */
class GameTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(TransactionType::cases());
        $isExpense = in_array($type, [
            TransactionType::HDMA,
            TransactionType::MARKETING_CAMPAIGN,
            TransactionType::MARKETING_TRAINING_BROAD,
            TransactionType::MARKETING_TRAINING_DEEP,
            TransactionType::LAB_CONSULTING,
            TransactionType::OPERATIONAL_COST,
        ]);
        
        return [
            'game_session_id' => GameSession::factory(),
            'type' => $type,
            'value' => $isExpense 
                ? fake()->numberBetween(-2000, -50) 
                : fake()->numberBetween(50, 5000),
            'round_id' => fake()->numberBetween(1, 20),
        ];
    }
}
