<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RequestVisit;

use App\Models\ScenarioClient;
use App\Traits\ListAndValidateClientForParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoRequestsController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        $participant = $request->participant();
        $requests = $client->listAvailableRequestForGameSession($participant->session);
        if ($requests->isNotEmpty()) {
            return redirect()->route('game.sales.request-visit.client', [$client]);
        }

        $hasMaxProjectsForCurrentYear = $client->hasMaxProjectsForCurrentYear($participant->session);

        return Inertia::render('game/sales/request-visit/no-requests', [
            'client' => $client,
            'hasMaxProjectsForCurrentYear' => $hasMaxProjectsForCurrentYear,
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }
}
