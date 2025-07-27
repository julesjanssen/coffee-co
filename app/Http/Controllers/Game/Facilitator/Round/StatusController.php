<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator\Round;

use App\Enums\GameSession\RoundStatus;
use App\Jobs\HandleRoundStart;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;

class StatusController
{
    public function store(Request $request)
    {
        $request->validate([
            'status' => ['required', 'string', new Enum(RoundStatus::class)],
        ]);

        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $status = $request->enum('status', RoundStatus::class);

        if ($status->is(RoundStatus::ACTIVE)) {
            $this->startNewRound($session);
        }

        return response(null, Response::HTTP_ACCEPTED);
    }

    private function startNewRound(GameSession $session)
    {
        $round = $session->currentRound->next();
        HandleRoundStart::dispatch($session, $round);
    }
}
