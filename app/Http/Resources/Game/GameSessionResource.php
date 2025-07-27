<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\GameSession;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin GameSession */
class GameSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'sqid' => $this->sqid,
            'publicID' => $this->public_id,
            'title' => $this->title,
            'roundStatus' => $this->round_status->toArray(),
            'status' => $this->status->toArray(),
            'currentRound' => $this->currentRound?->toArray(),
            'pausesAfterRound' => $this->settings->shouldPauseAfterCurrentRound,
            'links' => [
                'view' => route('game.sessions.view', [$this->public_id]),
            ],
            'sse' => [
                'topic' => $this->topicUrl(),
            ],
        ];
    }
}
