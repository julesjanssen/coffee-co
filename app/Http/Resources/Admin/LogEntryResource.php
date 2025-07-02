<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Support\Logs\LogEntry;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin LogEntry */
class LogEntryResource extends JsonResource
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
            'id' => $this->getId(),
            'message' => $this->message,
            'level' => $this->level,
            'levelName' => $this->levelName,
            'channel' => $this->channel,
            'datetime' => $this->datetime?->toISOString(),
            'context' => $this->context,
            'extra' => $this->extra,
            'hasException' => $this->hasException(),
            'isError' => $this->isError(),
            'isCritical' => $this->isCritical(),
            'userID' => $this->getUserId(),
            'tenantID' => $this->getTenantId(),
            'url' => $this->getUrl(),
            'ip' => $this->getIp(),
            'links' => [
                'view' => route('admin.system.logs.entry', [
                    'filename' => basename($request->route('filename')),
                    'id' => $this->getId(),
                ]),
            ],
        ];
    }
}
