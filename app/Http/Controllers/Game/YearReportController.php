<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Enums\Participant\Role;
use App\Models\GameParticipant;
use App\Values\GameRound;
use Illuminate\Http\Request;

class YearReportController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $round = $session->currentRound;
        if ($round->year() <= 1) {
            return response()->noContent();
        }

        $previousYearRound = $round->addRounds(GameRound::ROUNDS_PER_YEAR * -1);

        $message = $this->message($participant, $previousYearRound->year());
        if (empty($message)) {
            return response()->noContent();
        }

        return [
            'role' => $participant->role->toArray(),
            'year' => $previousYearRound->displayYear(),
            'message' => $message->render(),
        ];
    }

    private function message(GameParticipant $participant, int $year)
    {
        return match ($participant->role) {
            Role::MARKETING_1 => $this->messageMarketing($participant, $year),
            default => null,
        };
    }

    private function messageMarketing(GameParticipant $participant, int $year)
    {
        $session = $participant->session;
        $scenario = $session->scenario;

        return view('game/year-report/marketing', [
            'profit' => $participant->session->profit(),
            'campaignTarget' => $scenario->settings->targetMarketingCampaigns,
            'campaignCount' => 4,
            'year' => $year,
        ]);
    }
}
