<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GameSession\Status;
use App\Enums\Queue;
use App\Models\GameSession;
use App\Values\GameRound;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleGameSessionStart implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected GameSession $session,
    ) {
        $this->onQueue(Queue::GAME);
    }

    public function handle()
    {
        if ($this->session->status->isNot(Status::PENDING)) {
            return;
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
        $scenario = $this->session->pickRelevantScenario();

        $this->session->update([
            'status' => Status::PLAYING,
            'scenario_id' => $scenario->id,
        ]);

        $this->session->refresh();

        $round = new GameRound($this->session->scenario, 1);

        HandleRoundStart::dispatchSync($this->session, $round);
    }

    private function getReservationKey()
    {
        return self::class;
    }
}
