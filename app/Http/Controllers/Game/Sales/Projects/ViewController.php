<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\Projects;

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
            'project' => [
                'title' => $project->title,
                'price' => $project->price,
                'labConsultingApplied' => $project->settings->labConsultingApplied,
                'labConsultingIncluded' => $project->settings->labConsultingIncluded,
            ],
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }
}
