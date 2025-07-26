<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\GameSessions;

use App\Http\Resources\Admin\GameSessionViewResource;
use App\Models\GameSession;
use App\Models\Policies\GameSessionPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, GameSession $session)
    {
        Gate::authorize(GameSessionPolicy::VIEW, $session);

        $session->load([
            'participants',
            'facilitator',
        ]);

        return Inertia::render('game-sessions/view', [
            'session' => GameSessionViewResource::make($session),
            'links' => [
                'index' => route('admin.game-sessions.index'),
            ],
        ]);
    }
}
