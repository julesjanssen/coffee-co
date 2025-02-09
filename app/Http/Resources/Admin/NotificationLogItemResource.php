<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\NotificationLogItem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin NotificationLogItem */
class NotificationLogItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'sqid' => $this->sqid,
            // 'notifiable' => $this->whenLoaded('notifiable', fn() => $this->notifiable),
            'name' => $this->name(),
            'createdAt' => $this->created_at,
        ];
    }
}
