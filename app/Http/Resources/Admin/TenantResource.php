<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Tenant;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin Tenant */
class TenantResource extends JsonResource
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
            'name' => $this->name,
            'isCurrent' => $this->resource->isCurrent(),
            'links' => $this->when($this->resource->exists, fn() => [
                'view' => route('admin.tenants.view', $this->resource),
                'switch' => route('admin.tenants.switch', $this->resource),
            ]),
        ];
    }
}
