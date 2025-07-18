<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Policies\TenantPolicy;
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
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'sqid' => $this->sqid,
            'name' => $this->name,
            'isCurrent' => $this->resource->isCurrent(),
            'createdAt' => $this->created_at,
            'links' => $this->when($this->resource->exists, fn() => [
                'view' => route('admin.tenants.view', $this->resource),
                'update' => route('admin.tenants.update', $this->resource),
                'switch' => route('admin.tenants.switch', $this->resource),
            ]),
            'can' => $this->when(! is_null($request->user()), fn() => [
                TenantPolicy::VIEW => $request->user()->can(TenantPolicy::VIEW, $this->resource),
                TenantPolicy::UPDATE => $request->user()->can(TenantPolicy::UPDATE, $this->resource),
            ], []),
        ];
    }
}
