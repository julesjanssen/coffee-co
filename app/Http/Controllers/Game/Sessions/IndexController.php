<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sessions;

use App\Enums\GameSession\Status;
use App\Http\Resources\Game\GameSessionResource;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        $sessions = GameSession::query()
            ->whereIn('status', [
                Status::PENDING,
                Status::PLAYING,
            ])
            ->get();

        return Inertia::render('game/sessions/index', [
            'sessions' => GameSessionResource::collection($sessions),
        ]);
    }
}
