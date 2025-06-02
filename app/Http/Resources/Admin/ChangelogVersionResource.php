<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Support\Changelog\ChangelogVersion;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin ChangelogVersion */
class ChangelogVersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $sections = collect($this->resource->getEntriesByType())
            ->map(fn($v, $k) => [
                'type' => $k,
                'entries' => collect($v)->map(fn($v) => $v->description),
            ])
            ->values();

        return [
            'hash' => hash('xxh3', json_encode($sections)),
            'releasedAt' => $this->date,
            'version' => $this->version,
            'sections' => $sections,
        ];
    }
}
