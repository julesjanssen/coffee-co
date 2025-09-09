<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\SalesScreen;

use App\Enums\GameSession\TransactionType;
use App\Enums\Project\Status;
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

        $projectsPickedup = $session->projects()
            ->whereRelation('request', fn($q) => $q->where('delay', '>', 0))
            ->count();

        $projectsWon = $session->projects()
            ->whereRelation('request', fn($q) => $q->where('delay', '>', 0))
            ->whereNotIn('status', [
                Status::PENDING,
                Status::LOST,
            ])
            ->count();

        $investmentCost = $session->transactions()
            ->where('value', '<', 0)
            ->where('type', '<>', TransactionType::OPERATIONAL_COST)
            ->sum('value');

        return Inertia::render('game/sales-screen/results', [
            'projectsPickedup' => $projectsPickedup,
            'projectsWon' => $projectsWon,
            'investmentCost' => $investmentCost * -1,
            'clientsWithNps' => $session->netPromotorScorePerClient(),
        ]);
    }
}
