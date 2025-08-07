<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\ProjectAction;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin ProjectAction */
class ProjectActionResource extends JsonResource
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
            'project' => $this->whenLoaded('project', fn() => ProjectResource::make($this->project)),
            'type' => $this->type->toArray(),
            'details' => $this->details,
            'round' => $this->round->toArray(),
        ];
    }
}
