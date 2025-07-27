<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator\Session;

use App\Enums\GameSession\Status as GameSessionStatus;
use App\Jobs\HandleGameSessionStart;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;

class StatusController
{
    public function store(Request $request)
    {
        $request->validate([
            'status' => ['required', 'string', new Enum(GameSessionStatus::class)],
        ]);

        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $status = $request->enum('status', GameSessionStatus::class);

        if ($status->is(GameSessionStatus::PLAYING)) {
            HandleGameSessionStart::dispatch($session);
        }

        return response(null, Response::HTTP_ACCEPTED);
    }
}
