<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StatusController
{
    public function view(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        return Inertia::render('game/facilitator/status', [
            'settings' => $session->settings,
            'links' => [
                'sessionSettings' => route('game.facilitator.session-settings'),
                'sessionStatus' => route('game.facilitator.session-status'),
                'roundStatus' => route('game.facilitator.round-status'),
            ],
        ]);
    }
}
