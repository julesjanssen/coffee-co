<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RelationVisit;

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

        $mazeID = $participant->session->flow->mazeIdForScore();

        return Inertia::render('game/sales/relation-visit/client', [
            'client' => $client,
            'mazeID' => $mazeID,
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }

    public function store(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        // TODO: store NPS score update
        // TODO: keep track of used tips
        $tip = $participant->session->scenario->tips()
            ->inRandomOrder()
            ->first();

        return ['tip' => $tip->content];
    }
}
