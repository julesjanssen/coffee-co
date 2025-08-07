<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\Project;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin Project */
class ProjectResource extends JsonResource
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
            'client' => $this->whenLoaded('client', fn() => ScenarioClientResource::make($this->client)),
            'price' => $this->price,
            'labConsultingApplied' => $this->settings->labConsultingApplied,
            'labConsultingIncluded' => $this->settings->labConsultingIncluded,
            'status' => $this->status->toArray(),
            'failureChance' => $this->failure_chance,
            'location' => $this->location->toArray(),
        ];
    }
}
