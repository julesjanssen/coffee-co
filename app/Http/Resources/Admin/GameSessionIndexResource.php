<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Enums\Locale;
use App\Models\GameSession;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin GameSession */
class GameSessionIndexResource extends JsonResource
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
            'title' => $this->title,
            'scenario' => [
                /** @phpstan-ignore property.notFound */
                'title' => $this->scenario_title,
                /** @phpstan-ignore property.notFound */
                'locale' => Locale::from($this->scenario_locale)->toArray(),
            ],
            'status' => $this->status->toArray(),
            'currentRound' => $this->currentRound?->toArray(),
            'trashed' => $this->trashed(),
            'links' => $this->when($this->resource->exists, fn() => [
                'view' => route('admin.game-sessions.view', $this->resource),
            ]),
        ];
    }
}
