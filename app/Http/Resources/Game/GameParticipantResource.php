<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\GameHdmaActivation;
use App\Models\GameParticipant;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use JsonSerializable;

/** @mixin GameParticipant */
class GameParticipantResource extends JsonResource
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
            'type' => 'participant',
            'role' => $this->role->toArray(),
            'hdma' => $this->hdma(),
            'activeDuringBreak' => $this->role->isActiveDuringBreak(),
        ];
    }

    private function hdma()
    {
        if (! $this->role->isMarketing()) {
            return new MissingValue();
        }

        $this->loadMissing(['session']);

        $activeRounds = GameHdmaActivation::getContinuouslyActiveRounds($this->session);
        $effective = $activeRounds >= $this->session->settings->hdmaEffectiveRoundCount;

        return [
            'enabled' => $activeRounds > 0,
            'effective' => $effective,
        ];
    }
}
