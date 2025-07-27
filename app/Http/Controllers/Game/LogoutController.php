<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function store(Request $request)
    {
        Auth::guard('participant')->logout();
        Auth::guard('facilitator')->logout();

        return redirect()->route('game.sessions.index');
    }
}
