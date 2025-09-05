<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\GameSessions;

use App\Enums\GameSession\Status;
use App\Models\GameSession;
use App\Models\Policies\GameSessionPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class StatusController
{
    public function store(Request $request, GameSession $session)
    {
        Gate::authorize(GameSessionPolicy::VIEW, $session);

        $request->validate([
            'status' => ['required', 'string', new Enum(Status::class)],
        ]);

        $session->update([
            'status' => $request->enum('status', Status::class),
        ]);

        return response(null, Response::HTTP_ACCEPTED);
    }
}
