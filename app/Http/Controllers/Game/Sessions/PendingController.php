<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sessions;

use App\Http\Resources\Game\GameSessionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PendingController
{
    public function view(Request $request)
    {
        $session = $request->user()->session;
        if (! $session->isPending()) {
            return redirect()->route('game.base');
        }

        return Inertia::render('pending', [
            'session' => GameSessionResource::make($session),
        ]);
    }
}
