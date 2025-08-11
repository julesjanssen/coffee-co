<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\GameSession\RoundStatus;
use App\Enums\Queue;
use App\Models\GameSession;
use Duijker\LaravelMercureBroadcaster\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameSessionRoundStatusUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public RoundStatus $roundStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public GameSession $session,
    ) {
        $this->roundStatus = $session->round_status;
    }

    public function broadcastQueue(): string
    {
        return Queue::GAME->value;
    }

    public function broadcastWith()
    {
        return [
            'status' => $this->session->status,
            'roundStatus' => $this->roundStatus,
        ];
    }

    /**
     * @return Channel
     * @phpstan-ignore method.childReturnType
     */
    public function broadcastOn(): Channel
    {
        return new Channel($this->session->topicUrl());
    }
}
