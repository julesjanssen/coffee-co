<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator\Session;

use App\Enums\GameSession\Flow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsController
{
    public function store(Request $request)
    {
        $request->validate([
            'shouldPauseAfterCurrentRound' => ['sometimes', 'nullable', 'boolean'],
        ]);

        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        if ($request->has('shouldPauseAfterCurrentRound')) {
            $session->settings->shouldPauseAfterCurrentRound = $request->boolean('shouldPauseAfterCurrentRound');
        }

        if ($request->has('flow')) {
            $session->settings->flow = Flow::from($request->input('flow'));
        }

        if ($request->has('secondsPerRound')) {
            $session->settings->secondsPerRound = max(10, min(600, $request->integer('secondsPerRound')));
        }

        if ($request->has('hdmaEffectiveRoundCount')) {
            $session->settings->hdmaEffectiveRoundCount = max(6, min(24, $request->integer('hdmaEffectiveRoundCount')));
        }

        $session->save();

        if ($request->inertia()) {
            return back();
        }

        return response(null, Response::HTTP_ACCEPTED);
    }
}
