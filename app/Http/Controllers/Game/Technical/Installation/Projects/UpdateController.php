<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical\Installation\Projects;

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
            Status::WON,
        ])) {
            throw ValidationException::withMessages([
                'status' => ['No installation possible.'],
            ]);
        }

        ProjectAction::create([
            'game_session_id' => $project->session->id,
            'project_id' => $project->id,
            'type' => ActionType::INSTALLATION,
            'round_id' => $project->session->currentRound->roundID,
        ]);

        $project->update([
            'failure_chance' => 0,
            'delivery_round_id' => $project->session->currentRound->roundID,
            'status' => Status::ACTIVE,
            'down_round_id' => null,
        ]);

        return redirect()->route('game.technical.view');
    }
}
