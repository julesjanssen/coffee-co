<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice;

use App\Enums\GameSession\TransactionType;
use App\Enums\Project\Status;
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

        return Inertia::render('game/backoffice/results', [
            'projectsQuotedCount' => $this->projectsQuotedCount($session),
            'revenue' => $this->revenue($session),
            'operationalCosts' => $this->operationalCosts($session),
            'profit' => $this->profit($session),
            'uptime' => $session->averageUptime(),
            'investmentCosts' => $this->listInvestmentCosts($session),
            'npsPerClient' => $session->netPromotorScorePerClient(),
        ]);
    }

    private function projectsQuotedCount(GameSession $session)
    {
        $result = $session->projects()
            ->whereRelation('request', function ($q) {
                $q
                    ->where('delay', '>', 0)
                    ->orWhereNot('settings->initialstatus', Status::ACTIVE);
            })
            ->where('status', '!=', Status::PENDING)
            ->count();

        return (int) $result;
    }

    private function revenue(GameSession $session)
    {
        $revenue = $session->transactions()
            ->where('value', '>', 0)
            ->sum('value');

        return (int) $revenue;
    }

    private function operationalCosts(GameSession $session)
    {
        $operationalCosts = $session->transactions()
            ->where('type', '=', TransactionType::OPERATIONAL_COST)
            ->sum('value');

        return (int) $operationalCosts;
    }

    private function profit(GameSession $session)
    {
        return $session->profit();
    }

    private function listInvestmentCosts(GameSession $session)
    {
        return $session->listInvestmentCosts();
    }
}
