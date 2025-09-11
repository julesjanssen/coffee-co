<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Enums\Participant\Role;
use App\Models\GameParticipant;
use App\Values\GameYear;
use Illuminate\Http\Request;

class YearReportController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $round = $session->currentRound;
        if ($round->yearID() <= 1) {
            return response()->noContent();
        }

        $relevantYear = $round->year()->previous();

        $message = $this->message($participant, $relevantYear);
        if (empty($message)) {
            return response()->noContent();
        }

        return [
            'role' => $participant->role->toArray(),
            'year' => $relevantYear->display(),
            'message' => $message->render(),
        ];
    }

    private function message(GameParticipant $participant, GameYear $year)
    {
        return match ($participant->role) {
            Role::SALES_1,
            Role::SALES_2,
            Role::SALES_3,
            Role::SALES_SCREEN => $this->messageSales($participant, $year),
            Role::MARKETING_1 => $this->messageMarketing($participant, $year),
            default => null,
        };
    }

    private function messageSales(GameParticipant $participant, GameYear $year)
    {
        $session = $participant->session;
        $scenario = $session->scenario;

        $avgNps = $session->netPromotorScorePerClient()->pluck('nps')->avg();

        return view('game/year-report/sales', [
            'avgNps' => $avgNps,
            'profit' => $participant->session->profit(),
            'requestTarget' => $scenario->settings->targetSalesRequests,
            'requestCount' => 4,
            'year' => $year->display(),
        ]);
    }

    private function messageMarketing(GameParticipant $participant, GameYear $year)
    {
        $session = $participant->session;
        $scenario = $session->scenario;

        return view('game/year-report/marketing', [
            'profit' => $participant->session->profit(),
            'campaignTarget' => $scenario->settings->targetMarketingCampaigns,
            'campaignCount' => 4,
            'year' => $year->display(),
        ]);
    }
}
