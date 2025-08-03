<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Enums\Participant\Role;
use App\Http\Resources\Game\GameAuthenticatableResource;
use App\Jobs\HandleRoundStart;
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

        /** @var GameParticipant $participant */
        $participant = $user;

        if ($participant->role->in([
            Role::SALES_1,
            Role::SALES_2,
            Role::SALES_3,
        ])) {
            return redirect()->route('game.sales.view');
        }

        $session = $participant->session;
        // dd($session->settings);

        // HandleRoundStart::dispatchSync($session, $session->currentRound);

        // return $session;

        return Inertia::render('game/view', [
            'authenticatable' => GameAuthenticatableResource::make($user),
        ]);
    }
}
