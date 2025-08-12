<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\TechnicalScreen;

use App\Enums\Project\Status;
use App\Http\Resources\Game\ProjectResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectsController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $projects = $session->projects()
            ->with([
                'client',
                'request',
                'solution',
            ])
            ->filterAndOrderByStatus([
                Status::ACTIVE,
                Status::WON,
                Status::DOWN,
            ], 'desc')
            ->orderBy('failure_chance', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('game/technical-screen/projects', [
            'projects' => ProjectResource::collection($projects),
        ]);
    }
}
