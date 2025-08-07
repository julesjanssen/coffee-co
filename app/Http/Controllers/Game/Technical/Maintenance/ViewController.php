<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical\Maintenance;

use App\Enums\Project\Status;
use App\Http\Resources\Game\ProjectResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
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
            ->whereIn('status', [
                Status::ACTIVE,
                Status::DOWN,
            ])
            ->orderBy('quote_round_id', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    ...ProjectResource::make($project)->resolve(),
                    'href' => route('game.technical.maintenance.projects.update', [$project]),
                ];
            });

        return Inertia::render('game/technical/maintenance/view', [
            'projects' => $projects,
        ]);
    }
}
