<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical\Maintenance\Projects;

use App\Http\Resources\Game\ProjectActionResource;
use App\Http\Resources\Game\ProjectResource;
use App\Models\Project;
use App\Models\ProjectAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActionController
{
    public function view(Request $request, Project $project, ProjectAction $action)
    {
        if (! $action->project->is($project)) {
            throw new NotFoundHttpException();
        }

        $participant = $request->participant();
        $session = $participant->session;

        $action->setRelation('project', $project);
        $project->load(['client']);

        return Inertia::render('game/technical/maintenance/action', [
            'project' => ProjectResource::make($project),
            'projectAction' => ProjectActionResource::make($action),
            'mazeID' => $session->flow->mazeIdForScore(),
            'links' => [
                'back' => route('game.technical.view'),
                'extraService' => route('game.technical.maintenance.projects.action.extra-service', [
                    'project' => $project,
                    'action' => $action,
                ]),
            ],
        ]);
    }
}
