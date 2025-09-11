<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing;

use App\Enums\Scenario\CampaignCodeCategory;
use App\Models\GameCampaignCode;
use App\Models\GameHdmaActivation;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResultsController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        if (! $session->canDisplayResults()) {
            return redirect('/');
        }

        return Inertia::render('game/marketing/results', [
            'campaignStats' => $this->campaignStats($session),
            'hdmaActiveRounds' => $this->hdmaActiveRounds($session),
            'investmentCosts' => $this->listInvestmentCosts($session),
        ]);
    }

    private function hdmaActiveRounds(GameSession $session)
    {
        return GameHdmaActivation::getContinuouslyActiveRounds($session);
    }
    private function campaignStats(GameSession $session)
    {
        $result = GameCampaignCode::query()
            ->from('game_campaigncodes as gc')
            ->join('scenario_campaigncodes as sc', function ($q) {
                $q->on('sc.id', '=', 'gc.code_id');
            })
            /** @phpstan-ignore argument.type */
            ->where('gc.game_session_id', '=', $session->id)
            ->selectRaw('COUNT(*) as count_total')
            ->selectRaw('SUM(CASE WHEN sc.category = ? THEN 1 ELSE 0 END) AS count_difficult', [
                CampaignCodeCategory::DIFFICULT->value,
            ])
            ->toBase()
            ->first();

        return (object) [
            'total' => (int) $result->count_total,
            'difficult' => (int) $result->count_difficult,
        ];
    }

    private function listInvestmentCosts(GameSession $session)
    {
        return $session->listInvestmentCosts();
    }
}
