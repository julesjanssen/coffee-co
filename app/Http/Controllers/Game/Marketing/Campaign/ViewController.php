<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing\Campaign;

use App\Models\GameCampaignCode;
use App\Models\ScenarioCampaignCode;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        return Inertia::render('game/marketing/campaign/view');
    }

    public function store(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $request->validate([
            'code' => ['array', 'required', 'size:4'],
            'code.*' => ['required', 'integer', 'min:0', 'max:9'],
        ]);

        $code = $request->collect('code')->join('');

        $code = ScenarioCampaignCode::query()
            ->where('scenario_id', '=', $session->scenario_id)
            ->where('code', '=', $code)
            ->first();

        if (empty($code)) {
            throw ValidationException::withMessages([
                'code' => [__('Unknown code.')],
            ]);
        }

        $used = GameCampaignCode::query()
            ->where('game_session_id', '=', $session->id)
            ->where('code_id', '=', $code->id)
            ->exists();

        if ($used) {
            throw ValidationException::withMessages([
                'code' => [__('Code already used.')],
            ]);
        }

        $gameCode = GameCampaignCode::create([
            'game_session_id' => $session->id,
            'participant_id' => $participant->id,
            'code_id' => $code->id,
            'round_id' => $session->currentRound->roundID,
        ]);

        // TODO: track cost
        // TODO: track KPI score
        // TODO: track treshold score

        return redirect()->route('game.marketing.campaign.codes.view', [
            'numbers' => $code->code,
            'code' => $gameCode,
        ]);
    }
}
