<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\ScenarioClient;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin ScenarioClient */
class ScenarioClientResource extends JsonResource
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
        ];
    }
}
