<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RequestVisit;

use App\Traits\ListAndValidateClientForParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request)
    {
        $participant = $request->participant();

        $clients = $this->listClientsForParticipant($participant)
            ->map(fn($client) => [
                'title' => $client->title,
                'href' => route('game.sales.request-visit.client', [$client->sqid]),
            ]);

        return Inertia::render('game/sales/request-visit/view', [
            'clients' => $clients,
        ]);
    }
}
