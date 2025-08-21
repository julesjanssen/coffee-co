<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\GameSession;
use App\Models\Policies\GameSessionPolicy;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin GameSession */
class GameSessionViewResource extends JsonResource
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
            'scenario' => ScenarioResource::make($this->pickRelevantScenario()),
            'participants' => $this->whenLoaded('participants', fn() => GameParticipantResource::collection($this->participants)),
            'facilitator' => $this->whenLoaded('facilitator', fn() => GameFacilitatorResource::make($this->facilitator)),
            'status' => $this->status->toArray(),
            'currentRound' => $this->currentRound?->toArray(),
            'trashed' => $this->trashed(),
            'links' => $this->when($this->resource->exists, fn() => [
                'view' => route('admin.game-sessions.view', $this->resource),
                'update' => route('admin.game-sessions.view', $this->resource),
                'facilitatorLogin' => $this->whenLoaded('facilitator', fn() => $this->facilitatorLoginLink()),
            ]),
            'can' => $this->when(! is_null($request->user()), fn() => [
                GameSessionPolicy::UPDATE => $request->user()->can(GameSessionPolicy::UPDATE, $this->resource),
                GameSessionPolicy::DELETE => $request->user()->can(GameSessionPolicy::DELETE, $this->resource),
            ], []),
        ];
    }

    private function facilitatorLoginLink()
    {
        return route('game.facilitator.login', [
            $this->resource->public_id,
            $this->facilitator->loginHash(),
        ]);
    }
}
