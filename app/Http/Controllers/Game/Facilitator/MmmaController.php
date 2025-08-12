<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator;

use App\Enums\GameSession\TransactionType;
use App\Models\GameMmmaActivation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MmmaController
{
    public function view(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $activeRounds = GameMmmaActivation::getContinuouslyActiveRounds($session);

        return Inertia::render('game/facilitator/mmma', [
            'activeRounds' => $activeRounds,
            'effectiveRounds' => $session->settings->mmmaEffectiveRoundCount,
        ]);
    }

    public function store(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $currentRoundID = $session->currentRound->roundID;

        $offset = 0;
        while ($offset <= $session->settings->mmmaEffectiveRoundCount) {
            $roundID = max(0, $currentRoundID - $offset);

            GameMmmaActivation::create([
                'game_session_id' => $session->id,
                'details' => [
                    'source' => 'facilitator',
                ],
                'round_id' => $roundID,
            ]);

            $cost = config('coffeeco.mmma_cost', 75);
            $session->transactions()
                ->create([
                    'type' => TransactionType::MMMA,
                    'round_id' => $roundID,
                    'value' => $cost * -1,
                ]);

            $offset += $session->settings->mmmaEnabledRoundCount;
        }

        return response()->noContent();
    }
}
