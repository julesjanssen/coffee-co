<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $route = $participant->role->mainRoute();

        if (empty($route)) {
            throw new BadRequestHttpException();
        }

        return redirect($route);
    }
}
