<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales;

use App\Http\Resources\Game\ScenarioClientResource;
use App\Models\GameParticipant;
use App\Models\ScenarioClient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestVisitController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $clients = $this->listClients($participant)
            ->map(fn($client) => [
                'title' => $client->title,
                'href' => route('game.sales.request-visit.client', [$client->sqid]),
            ]);

        return Inertia::render('game/sales/request-visit/view', [
            'clients' => $clients,
        ]);
    }

    public function client(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();
        $clients = $this->listClients($participant);

        if (! $clients->contains($client)) {
            throw new BadRequestHttpException;
        }

        $requests = $client->listAvailableRequestForGameSession($participant->session);

        return Inertia::render('game/sales/request-visit/client', [
            'client' => $client,
            'requests' => $requests,
        ]);
    }

    private function listClients(GameParticipant $participant)
    {
        return $participant->session->scenario->clients()
            ->where('player_id', '=', $participant->role->playerID())
            ->get();
    }
}
