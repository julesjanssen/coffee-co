<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Facilitator;

use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectsController
{
    public function view(Request $request)
    {
        $facilitator = $request->facilitator();
        $session = $facilitator->session;

        return Inertia::render('game/facilitator/projects', [
            'projects' => $this->listProjects($session),
        ]);
    }

    private function listProjects(GameSession $session)
    {
        $scenario = $session->scenario;

        return $session->projects()
            ->with([
                'request',
                'client',
                'solution',
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($project) => [
                'title' => $project->title,
                'status' => $project->status->toArray(),
                'price' => $project->price,
                'requestRound' => $project->requestRound,
                'quoteRound' => $project->quoteRound,
                'deliveryRound' => $project->deliveryRound,
            ]);
    }
}
