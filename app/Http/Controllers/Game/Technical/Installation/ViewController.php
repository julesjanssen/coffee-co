<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical\Installation;

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
                Status::WON,
            ])
            ->orderBy('quote_round_id', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    ...ProjectResource::make($project)->resolve(),
                    'href' => route('game.technical.installation.projects.update', [$project]),
                ];
            });

        return Inertia::render('game/technical/installation/view', [
            'projects' => $projects,
        ]);
    }
}
