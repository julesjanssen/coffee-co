<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical\Maintenance\Projects;

use App\Enums\Project\ActionType;
use App\Enums\Project\Status;
use App\Models\Project;
use App\Models\ProjectAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UpdateController
{
    public function store(Request $request, Project $project)
    {
        if ($project->status->notIn([
            Status::ACTIVE,
            Status::DOWN,
        ])) {
            throw ValidationException::withMessages([
                'status' => ['No maintenance necessary.'],
            ]);
        }

        $action = $project->status->is(Status::DOWN)
            ? $this->fixProject($project)
            : $this->maintainProject($project);

        return redirect()->route('game.technical.maintenance.projects.action.view', [
            'project' => $project,
            'action' => $action,
        ]);
    }

    private function maintainProject(Project $project)
    {
        $action = ProjectAction::create([
            'game_session_id' => $project->session->id,
            'project_id' => $project->id,
            'type' => ActionType::MAINTENANCE,
            'round_id' => $project->session->currentRound->roundID,
        ]);

        $project->update([
            'failure_chance' => 0,
        ]);

        return $action;
    }

    private function fixProject(Project $project)
    {
        $action = ProjectAction::create([
            'game_session_id' => $project->session->id,
            'project_id' => $project->id,
            'type' => ActionType::FIX,
            'round_id' => $project->session->currentRound->roundID,
        ]);

        $project->update([
            'failure_chance' => 0,
            'status' => Status::ACTIVE,
            'down_round_id' => null,
        ]);

        return $action;
    }
}
