<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sessions;

use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitatorController
{
    public function login(Request $request, string $session, string $hash)
    {
        $session = $this->getSessionFromPublicID($session);
        $facilitator = $session->facilitator;

        if (! hash_equals($facilitator->loginHash(), $hash)) {
            return redirect()->route('game.sessions.view', [$session]);
        }

        Auth::guard('facilitator')->login($facilitator);

        return redirect('/game');
    }

    private function getSessionFromPublicID(string $session)
    {
        return GameSession::query()
            ->where('public_id', '=', $session)
            ->firstOrFail();
    }
}
