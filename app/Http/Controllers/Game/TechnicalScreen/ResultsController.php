<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\TechnicalScreen;

use App\Enums\GameSession\TransactionType;
use App\Enums\Project\Status;
use App\Http\Resources\Game\ScenarioClientResource;
use App\Models\GameSession;
use App\Models\ProjectHistoryItem;
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

        return Inertia::render('game/technical-screen/results', [
            'uptime' => $this->getUptime($session),
            'uptimeBonus' => $this->getUptimeBonus($session),
            'uptimePerClient' => $this->listUptimePerClient($session),
        ]);
    }

    private function getUptime(GameSession $session)
    {
        return $session->averageUptime();
    }

    private function getUptimeBonus(GameSession $session)
    {
        $result = $session->transactions()
            ->where('type', '=', TransactionType::PROJECT_UPTIME_BONUS)
            ->sum('value');

        return (int) $result;
    }

    private function listUptimePerClient(GameSession $session)
    {
        $clients = $session->scenario
            ->clients
            ->keyBy('id');

        return ProjectHistoryItem::query()
            ->join('projects as p', function ($query) {
                $query->on('p.id', '=', 'project_history.project_id');
            })
            /** @phpstan-ignore argument.type */
            ->where('p.game_session_id', '=', $session->id)
            ->whereIn('project_history.status', [
                Status::ACTIVE,
                Status::DOWN,
            ])
            ->select('p.client_id')
            ->selectAverageUptime()
            ->groupBy('client_id')
            ->orderBy('client_id')
            ->toBase()
            ->get()
            ->map(fn($v) => [
                'uptime' => (float) $v->avg_uptime,
                'client' => ScenarioClientResource::make($clients->get($v->client_id)),
            ]);
    }
}
