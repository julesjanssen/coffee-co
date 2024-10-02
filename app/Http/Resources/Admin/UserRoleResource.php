<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Auth\Role;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Role */
class UserRoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
