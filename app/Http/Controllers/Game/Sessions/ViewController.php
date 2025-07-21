<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sessions;

use App\Enums\Participant\Role;
use App\Http\Resources\Game\GameParticipantResource;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, string $session)
    {
        $session = $this->getSessionFromPublicID($session);

        $participant = Auth::guard('participant')->user();
        if (! empty($participant)) {
            return redirect()->route('game.base');
        }

        $facilitator = Auth::guard('facilitator')->user();
        if (! empty($facilitator)) {
            return redirect()->route('game.base');
        }

        return Inertia::render('game/sessions/view', [
            'participants' => GameParticipantResource::collection($session->participants),
        ]);
    }

    public function store(Request $request, string $session)
    {
        $session = $this->getSessionFromPublicID($session);

        if ($request->input('role') === 'facilitator') {
            return $this->handleFacilitatorLogin($request, $session);
        }

        return $this->handleParticipantLogin($request, $session);
    }

    private function handleFacilitatorLogin(Request $request, GameSession $session)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:100'],
        ]);

        $code = strtolower((string) $request->input('code'));
        if ($session->facilitator->code !== $code) {
            throw ValidationException::withMessages([
                'code' => [__('Invalid facilitator code.')],
            ]);
        }

        Auth::guard('facilitator')->login($session->facilitator);

        return redirect('/game');
    }

    private function handleParticipantLogin(Request $request, GameSession $session)
    {
        $request->validate([
            'role' => ['required', 'string', new Enum(Role::class)],
        ]);

        $participant = $session->participants()
            ->where('role', '=', $request->input('role'))
            ->firstOrFail();

        Auth::guard('participant')->login($participant);

        return redirect('/game');
    }

    private function getSessionFromPublicID(string $session)
    {
        return GameSession::query()
            ->where('public_id', '=', $session)
            ->firstOrFail();
    }
}
