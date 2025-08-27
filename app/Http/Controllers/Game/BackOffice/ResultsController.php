<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ResultsController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $clients = $session->scenario->clients
            ->map(fn($c) => [
                'sqid' => $c->sqid,
                'title' => $c->title,
                'nps' => $c->netPromotorScoreForGameSession($session),
            ]);

        return Inertia::render('game/backoffice/results', [
            'clients' => $clients,
        ]);
    }
}
