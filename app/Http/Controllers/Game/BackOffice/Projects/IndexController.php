<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice\Projects;

use App\Enums\Project\Status;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $projects = $this->listProjects($session)
            ->map(fn($project) => [
                'title' => $project->title,
                'client' => [
                    'title' => $project->request->client->title,
                ],
                'price' => $project->price,
                'shouldBeQuotedBy' => $project->shouldBeQuotedBy(),
                'href' => route('game.backoffice.projects.view', [$project]),
            ]);

        return Inertia::render('game/backoffice/projects/index', [
            'projects' => $projects,
        ]);
    }

    private function listProjects(GameSession $session)
    {
        return $session->projects()
            ->with([
                'request',
                'request.client',
            ])
            ->where('status', '=', Status::PENDING)
            ->orderBy('request_round_id', 'asc')
            ->orderBy('id', 'desc')
            ->get();
    }
}
