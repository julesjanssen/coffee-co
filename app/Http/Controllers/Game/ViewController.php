<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Http\Resources\Game\GameAuthenticatableResource;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $user = $request->user();
        if ($user instanceof GameFacilitator) {
            return redirect()->route('game.facilitator.status');
        }

        /** @var GameParticipant $user */
        // $session = $user->session;

        return Inertia::render('game/view', [
            'authenticatable' => GameAuthenticatableResource::make($user),
        ]);
    }
}
