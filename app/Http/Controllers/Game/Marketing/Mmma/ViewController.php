<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing\Mmma;

use App\Models\GameMmmaActivation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $participant = $request->participant();

        return Inertia::render('game/marketing/mmma/view', [
            'mazeLevel' => $participant->session->flow->mazeLevelForScore(),
        ]);
    }

    public function store(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        if (! $session->canRefreshMmma()) {
            throw ValidationException::withMessages([
                'status' => __('It is not possible to refresh MMMA yet.'),
            ]);
        }

        GameMmmaActivation::create([
            'game_session_id' => $session->id,
            'participant_id' => $participant->id,
            'details' => [],
            'round_id' => $session->currentRound->roundID,
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
