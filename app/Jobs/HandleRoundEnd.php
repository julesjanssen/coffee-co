<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\Status;
use App\Enums\Queue;
use App\Events\GameSessionRoundStatusUpdated;
use App\Models\GameSession;
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

        // track uptime, actions etc
        // schedule round-start or pause
        if ($this->round->isLastRoundOfQuarter()) {
            $this->processNpsDeltasForUptime();
        }

        if ($this->round->isLastRoundOfYear()) {
            $this->trackMarketShare();
        }
    }

    private function postProcessing()
    {
        $this->session->round_status = RoundStatus::PROCESSED;

        if ($this->round->roundID >= $this->session->scenario->numberOfRounds()) {
            $this->session->status = Status::FINISHED;
        }

        if ($this->session->settings->shouldPauseAfterCurrentRound) {
            $this->session->round_status = RoundStatus::PAUSED;
        } else {
            // start new round
            HandleRoundStart::dispatch($this->session, $this->round->next());
        }

        GameSessionRoundStatusUpdated::dispatch($this->session);
    }

    private function processProjects()
    {
        return random_int(0, 10);
    }

    private function processNpsDeltasForUptime()
    {
        // calc uptimes for last quarter & distribute NPS deltas
        return random_int(0, 10);
    }

    private function trackMarketShare()
    {
        // nothing yet
        return random_int(0, 10);
    }

    private function getReservationKey()
    {
        return self::class . ':' . $this->round->roundID;
    }
}
