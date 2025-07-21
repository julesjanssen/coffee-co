<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\GameFacilitator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin GameFacilitator */
class GameFacilitatorResource extends JsonResource
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
            'type' => 'facilitator',
            'sqid' => $this->sqid,
        ];
    }
}
