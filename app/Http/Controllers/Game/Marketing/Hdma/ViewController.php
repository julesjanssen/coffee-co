<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing\Hdma;

use App\Enums\GameSession\TransactionType;
use App\Models\GameHdmaActivation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $participant = $request->participant();

        return Inertia::render('game/marketing/hdma/view', [
            'mazeLevel' => $participant->session->flow->mazeLevelForScore(),
            'links' => [
                'back' => route('game.marketing.view'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        if (! $session->canRefreshHdma()) {
            throw ValidationException::withMessages([
                'status' => __('It is not possible to refresh HDMA yet.'),
            ]);
        }

        GameHdmaActivation::create([
            'game_session_id' => $session->id,
            'participant_id' => $participant->id,
            'details' => [],
            'round_id' => $session->currentRound->roundID,
        ]);

        $cost = config('coffeeco.hdma_cost', 75);
        $session->transactions()
            ->create([
                'participant_id' => $participant->id,
                'type' => TransactionType::HDMA,
                'round_id' => $session->currentRound->roundID,
                'value' => $cost * -1,
            ]);

        $date = $session->currentRound->addRounds(6);

        $messages = [
            __('You are now using pre-influence to receive new requests'),
            __('To keep this active, refresh in or before :date.', ['date' => $date->displayFull()]),
        ];

        return [
            'hints' => $messages,
        ];
    }
}
