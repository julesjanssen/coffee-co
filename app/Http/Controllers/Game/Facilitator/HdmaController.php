<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator;

use App\Enums\GameSession\TransactionType;
use App\Models\GameHdmaActivation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HdmaController
{
    public function view(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $activeRounds = GameHdmaActivation::getContinuouslyActiveRounds($session);

        return Inertia::render('game/facilitator/hdma', [
            'activeRounds' => $activeRounds,
            'effectiveRounds' => $session->settings->hdmaEffectiveRoundCount,
        ]);
    }

    public function store(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $currentRoundID = $session->currentRound->roundID;

        $offset = 0;
        while ($offset <= $session->settings->hdmaEffectiveRoundCount) {
            $roundID = max(0, $currentRoundID - $offset);

            GameHdmaActivation::create([
                'game_session_id' => $session->id,
                'details' => [
                    'source' => 'facilitator',
                ],
                'round_id' => $roundID,
            ]);

            $cost = config('coffeeco.hdma_cost', 75);
            $session->transactions()
                ->create([
                    'type' => TransactionType::HDMA,
                    'round_id' => $roundID,
                    'value' => $cost * -1,
                ]);

            $offset += $session->settings->hdmaEnabledRoundCount;
        }

        return response()->noContent();
    }
}
