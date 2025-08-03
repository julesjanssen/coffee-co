<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales;

use App\Http\Resources\Game\ScenarioClientResource;
use App\Models\GameParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestVisitController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $clients = $this->listClients($participant);

        // $requests = $clients->first()->listAvailableRequestForGameSession($participant->session);

        return Inertia::render('game/sales/request-visit', [
            'clients' => ScenarioClientResource::collection($clients),
        ]);
    }

    public function details(Request $request)
    {
        if ($request->filled('client')) {
            return $this->getClientDetails($request);
        }
    }

    public function getClientDetails(Request $request)
    {
        $participant = $request->participant();

        $client = $participant
            ->session->scenario->clients()
            ->whereSqid($request->input('client'));

        dd($client);
    }

    private function listClients(GameParticipant $participant)
    {
        return $participant->session->scenario->clients()
            ->where('player_id', '=', $participant->role->playerID())
            ->get();
    }
}
