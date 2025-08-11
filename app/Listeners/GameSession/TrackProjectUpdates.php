<?php

declare(strict_types=1);

namespace App\Listeners\GameSession;

use App\Events\ProjectUpdated;

class TrackProjectUpdates
{
    public function handle(ProjectUpdated $event)
    {
        $project = $event->project;

        $changes = $project->getChanges();
        if (array_key_exists('status', $changes)) {
            $project->historyItems()->create([
                'round_id' => $project->session->currentRound->roundID,
                'status' => $project->status,
            ]);
        }
    }
}
