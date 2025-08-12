<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Materials;

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
                Status::WON,
                Status::DOWN,
                Status::ACTIVE,
            ], 'desc')
            ->orderBy('quote_round_id', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('game/materials/projects', [
            'projects' => ProjectResource::collection($projects),
        ]);
    }
}
