<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function store(Request $request)
    {
        $user = $request->user();
        $session = null;

        if (! empty($user)) {
            $session = $user->session;
        }

        Auth::guard('participant')->logout();
        Auth::guard('facilitator')->logout();

        if (! empty($session)) {
            return redirect()->route('game.sessions.view', [$session->public_id]);
        }

        return redirect()->route('game.base');
    }
}
