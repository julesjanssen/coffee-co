<?php

declare(strict_types=1);

namespace App\Listeners\GameSession;

use App\Enums\Project\Status;
use App\Events\ProjectUpdated;

class TrackProjectUpdates
{
    public function handle(ProjectUpdated $event)
    {
        $project = $event->project;

        if ($project->status->in([
            Status::ACTIVE,
            Status::DOWN,
        ])) {
            // these will be tracked at the end of the month
            return;
        }

        $changes = $project->getChanges();
        if (array_key_exists('status', $changes)) {
            $project->historyItems()->create([
                'round_id' => $project->session->currentRound->roundID,
                'status' => $project->status,
            ]);
        }
    }
}
