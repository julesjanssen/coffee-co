<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\GameSession;
use App\Models\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin GameSession */
class GameSessionUpdateResource extends JsonResource
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
            'scenarioGroupID' => $this->scenario_group_id,
            'publicID' => $this->public_id,
            'title' => $this->title,
            'status' => $this->status->toArray(),
            'currentRound' => $this->currentRound?->toArray(),
            'trashed' => $this->trashed(),
            'links' => $this->when($this->resource->exists, fn() => [
                'view' => route('admin.game-sessions.view', $this->resource),
            ]),
        ];
    }
}
