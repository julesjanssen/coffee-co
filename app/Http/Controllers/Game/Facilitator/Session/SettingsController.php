<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator\Session;

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

        $session->save();

        return response(null, Response::HTTP_ACCEPTED);
    }
}
