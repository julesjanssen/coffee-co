<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing;

use App\Models\GameCampaignCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InfoController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $hints = GameCampaignCode::query()
            ->where('game_session_id', '=', $session->id)
            ->orderBy('round_id', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->flatMap(function ($result) {
                return $result->details->hints;
            });

        return Inertia::render('game/marketing/info', [
            'hints' => $hints,
        ]);
    }
}
