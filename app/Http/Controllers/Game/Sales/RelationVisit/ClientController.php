<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RelationVisit;

use App\Enums\GameSession\ScoreType;
use App\Models\ScenarioClient;
use App\Traits\ListAndValidateClientForParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        return Inertia::render('game/sales/relation-visit/client', [
            'client' => $client,
            'mazeLevel' => $participant->session->flow->mazeLevelForScore(),
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }

    public function store(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $this->validateClientForParticipant($participant, $client);

        $session->scores()
            ->create([
                'participant_id' => $participant->id,
                'type' => ScoreType::NPS,
                'trigger_type' => 'relation-visit',
                'event' => 'relation-visit',
                'round_id' => $session->currentRound->roundID,
                'value' => 3,
            ]);

        $tip = $participant->session->scenario->tips()
            ->inRandomOrder()
            ->first();

        return ['tip' => $tip->content];
    }
}
