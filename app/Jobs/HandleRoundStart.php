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

class HandleRoundStart implements ShouldQueue
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
        if ($this->session->current_round_id >= $this->round->roundID) {
            return;
        }

        if ($this->session->status->isNot(Status::PENDING)) {
            if ($this->session->round_status->notIn([
                RoundStatus::PROCESSED,
                RoundStatus::PAUSED,
            ])) {
                return;
            }
        }

        if (! $this->session->reserve($this->getReservationKey(), '+30 seconds')) {
            $this->release(10);

            return;
        }

        $this->process();

        $this->session->release($this->getReservationKey());
    }

    private function process()
    {
        if ($this->round->isFirstRoundOfYear()) {
            // TODO: process operational cost per year on first month of year
        }

        if ($this->round->isLastRoundOfYear()) {
            $this->session->settings->shouldPauseAfterCurrentRound = true;
        }

        $this->session->round_status = RoundStatus::ACTIVE;
        $this->session->current_round_id = $this->round->roundID;
        $this->session->save();

        GameSessionRoundStatusUpdated::dispatch($this->session);

        HandleRoundEnd::dispatch($this->session, $this->round)->delay($this->session->settings->secondsPerRound);
    }

    private function getReservationKey()
    {
        return self::class . ':' . $this->round->roundID;
    }
}
