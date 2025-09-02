<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Scenario;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin Scenario */
class ScenarioResource extends JsonResource
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
            'locale' => $this->locale->toArray(),
            'status' => $this->status->toArray(),
            'createdAt' => $this->created_at,
        ];
    }
}
