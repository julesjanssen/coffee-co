<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Support\Changelog\ChangelogVersion;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ChangelogVersion */
class ChangelogVersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
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
