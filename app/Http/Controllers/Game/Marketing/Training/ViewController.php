<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing\Training;

use App\Enums\GameSession\TrainingType;
use App\Models\GameTraining;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, TrainingType $type)
    {
        $participant = $request->participant();

        return Inertia::render('game/marketing/training/view', [
            'type' => $type,
            'mazeLevel' => $participant->session->flow->mazeLevelForScore(),
        ]);
    }

    public function store(Request $request, TrainingType $type)
    {
        $participant = $request->participant();
        $session = $participant->session;

        GameTraining::create([
            'game_session_id' => $session->id,
            'participant_id' => $participant->id,
            'type' => $type,
            'round_id' => $session->currentRound->roundID,
        ]);

        $session->transactions()
            ->create([
                'participant_id' => $participant->id,
                'type' => $type->transactionType(),
                'round_id' => $session->currentRound->roundID,
                'value' => $type->cost() * -1,
            ]);

        $cardID = GameTraining::query()
            ->where('game_session_id', '=', $session->id)
            ->where('type', '=', $type)
            ->count();

        if ($cardID > $type->maxCampaigns()) {
            $message = __('There are no more trainings with new information available.');
        } else {
            $message = __('Congratulations, pick the materials for training card :cardID.', ['cardID' => $cardID]);
        }

        return [
            'hints' => [$message],
        ];
    }
}
