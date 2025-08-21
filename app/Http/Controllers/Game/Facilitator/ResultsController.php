<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator;

use App\Enums\GameSession\ScoreType;
use App\Enums\GameSession\TransactionType;
use App\Models\GameScore;
use App\Models\GameSession;
use App\Models\GameTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ResultsController
{
    public function view(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        $session
            ->load([
                'scenario',
                'scenario.clients',
                'scores',
            ]);

        return Inertia::render('game/facilitator/results', [
            'clients' => $this->listClients($session),
            'hdmaActive' => $session->isHDMAActive(),
            'marketing' => $this->marketingScores($session),
            'profitLoss' => $this->profitLoss($session),
            'investmentCosts' => $this->investmentCosts($session),
        ]);
    }

    private function listClients(GameSession $session)
    {
        return $session->scenario->clients
            ->map(fn($client) => [
                'sqid' => $client->sqid,
                'title' => $client->title,
                'nps' => $client->netPromotorScoreForGameSession($session),
            ]);
    }

    private function marketingScores(GameSession $session)
    {
        $result = GameScore::query()
            ->whereBelongsTo($session, 'session')
            ->selectRaw('SUM(CASE WHEN type = ? THEN value ELSE 0 END) AS kpi_sum', [ScoreType::MARKETING_KPI])
            ->selectRaw('SUM(CASE WHEN type = ? THEN value ELSE 0 END) AS treshold_sum', [ScoreType::MARKETING_TRESHOLD])
            ->toBase()
            ->first();

        return [
            'kpi' => (int) $result->kpi_sum,
            'treshold' => (int) $result->treshold_sum,
        ];
    }

    private function profitLoss(GameSession $session)
    {
        $result = GameTransaction::query()
            ->whereBelongsTo($session, 'session')
            ->selectRaw('SUM(CASE WHEN value > 0 THEN value ELSE 0 END) AS revenue')
            ->selectRaw('SUM(CASE WHEN value < 0 THEN value ELSE 0 END) AS costs')
            ->toBase()
            ->first();

        return [
            'revenue' => (int) $result->revenue,
            'costs' => (int) $result->costs,
            'profit' => (int) $result->revenue + (int) $result->costs,
        ];
    }

    private function investmentCosts(GameSession $session)
    {
        $types = [
            TransactionType::LAB_CONSULTING,
            TransactionType::HDMA,
            TransactionType::MARKETING_CAMPAIGN,
            TransactionType::MARKETING_TRAINING_BROAD,
            TransactionType::MARKETING_TRAINING_DEEP,
        ];

        $selects = [];
        foreach ($types as $type) {
            $selects[] = DB::raw(vsprintf("SUM(CASE WHEN type = '%s' THEN value ELSE 0 END) AS %s", [
                $type->value,
                $type->dbAlias(),
            ]));
        }

        $result = GameTransaction::query()
            ->whereBelongsTo($session, 'session')
            ->whereIn('type', $types)
            ->select($selects)
            ->toBase()
            ->first();

        $results = collect($types)
            ->mapWithKeys(fn($t) => [$t->value => $result->{$t->dbAlias()} * -1]);

        $results->put('total', $results->sum());

        return $results;
    }
}
