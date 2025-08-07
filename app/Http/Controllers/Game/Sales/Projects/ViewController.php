<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\Projects;

use App\Http\Resources\Game\ProjectResource;
use App\Models\Project;
use App\Traits\ListAndValidateClientForParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request, Project $project)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $project->client);

        return Inertia::render('game/sales/projects/view', [
            'client' => ['title' => $project->client->title],
            'project' => ProjectResource::make($project),
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }
}
