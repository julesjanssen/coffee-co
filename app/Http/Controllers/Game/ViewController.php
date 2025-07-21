<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Http\Resources\Game\GameAuthenticatableResource;
use App\Http\Resources\Game\GameSessionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $user = $request->user();
        $session = $user->session;

        return Inertia::render('game/view', [
            'authenticatable' => GameAuthenticatableResource::make($user),
            'session' => GameSessionResource::make($session),
        ]);
    }
}
