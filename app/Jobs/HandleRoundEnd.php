<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\Status;
use App\Enums\Project\Status as ProjectStatus;
use App\Enums\Queue;
use App\Events\GameSessionRoundStatusUpdated;
use App\Models\GameSession;
use App\Models\Project;
use App\Values\GameRound;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleRoundEnd implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected GameSession $session,
        protected GameRound $round,
    ) {
        $this->onQueue(Queue::GAME);
    }

    public function handle()
    {
        if ($this->session->current_round_id !== $this->round->roundID) {
            return;
        }

        if ($this->session->round_status->isNot(RoundStatus::ACTIVE)) {
            return;
        }

        if (! $this->session->reserve($this->getReservationKey(), '+30 seconds')) {
            $this->release(10);

            return;
        }

        $this->preProcessing();
        $this->process();
        $this->postProcessing();

        $this->session->release($this->getReservationKey());
    }

    private function preProcessing()
    {
        $this->session->update([
            'round_status' => RoundStatus::PROCESSING,
        ]);

        GameSessionRoundStatusUpdated::dispatch($this->session);
    }

    private function process()
    {
        $this->processProjects();

        if ($this->round->isLastRoundOfQuarter()) {
            $this->processNpsDeltasForUptime();
        }

        if ($this->round->isLastRoundOfYear()) {
            $this->trackMarketShare();
        }

        $this->session->update([
            'round_status' => RoundStatus::PROCESSED,
        ]);
    }

    private function postProcessing()
    {
        $this->session->update([
            'round_status' => RoundStatus::PROCESSED,
        ]);

        if ($this->round->roundID >= $this->session->scenario->numberOfRounds()) {
            $this->session->update([
                'status' => Status::FINISHED,
            ]);
        }

        if ($this->session->settings->shouldPauseAfterCurrentRound) {
            $this->session->pause();
        } else {
            // start new round
            HandleRoundStart::dispatch($this->session, $this->round->next());
        }

        GameSessionRoundStatusUpdated::dispatch($this->session);
    }

    private function processProjects()
    {
        $this->updateProjectFailureChances();
        $this->updateProjectStates();
    }

    private function updateProjectFailureChances()
    {
        $this->session->projects()
            ->where('status', '=', ProjectStatus::ACTIVE)
            ->increment('failure_chance', $this->session->settings->failChanceIncreasePerRound);
    }

    private function updateProjectStates()
    {
        $this->session->projects()
            ->with(['request'])
            ->whereNotIn('status', [
                ProjectStatus::FINISHED,
                ProjectStatus::LOST,
            ])
            ->get()
            ->each($this->updateProjectState(...))
            // TODO: track up/downtime in project-history
            // TODO: calc uptime
            // ->each($this->calcUptime(...))
            ->each($this->handleProjectLifetime(...));
    }

    private function updateProjectState(Project $project)
    {
        switch ($project->status) {
            case ProjectStatus::ACTIVE:
                if (random_int(0, 100) < $project->failure_chance) {
                    $project->update([
                        'status' => ProjectStatus::DOWN,
                        'down_round_id' => $this->round->roundID,
                    ]);
                }
                break;

            case ProjectStatus::DOWN:
                $project->increment('downtime');
                break;

            case ProjectStatus::WON:
                // TODO: check if project is delivered in time
                break;

            case ProjectStatus::PENDING:
                // TODO: check if offer is submitted in time
                break;
        }
    }

    private function handleProjectLifetime(Project $project)
    {
        if ($project->status->notIn([
            ProjectStatus::ACTIVE,
            ProjectStatus::DOWN,
        ])) {
            return;
        }

        // TODO: check lifetime, calc bonus & set to finished
    }

    private function processNpsDeltasForUptime()
    {
        // TODO: calc uptimes for last quarter & distribute NPS deltas
        return random_int(0, 10);
    }

    private function trackMarketShare()
    {
        // TODO: implement
        return random_int(0, 10);
    }

    private function getReservationKey()
    {
        return self::class . ':' . $this->round->roundID;
    }
}
