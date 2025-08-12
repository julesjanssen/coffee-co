<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Enums\Participant\Role;
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

        if ($participant->role->in([
            Role::SALES_1,
            Role::SALES_2,
            Role::SALES_3,
        ])) {
            return redirect()->route('game.sales.view');
        }

        if ($participant->role->in([
            Role::SALES_SCREEN,
        ])) {
            return redirect()->route('game.sales-screen.projects');
        }

        if ($participant->role->in([
            Role::TECHNICAL_1,
            Role::TECHNICAL_2,
        ])) {
            return redirect()->route('game.technical.view');
        }

        if ($participant->role->in([
            Role::TECHNICAL_SCREEN,
        ])) {
            return redirect()->route('game.technical-screen.projects');
        }

        if ($participant->role->in([
            Role::MARKETING_1,
        ])) {
            return redirect()->route('game.marketing.view');
        }

        if ($participant->role->in([
            Role::BACKOFFICE_1,
        ])) {
            return redirect()->route('game.backoffice.view');
        }

        if ($participant->role->in([
            Role::MATERIALS_1,
        ])) {
            return redirect()->route('game.materials.projects');
        }

        throw new BadRequestHttpException();
    }
}
