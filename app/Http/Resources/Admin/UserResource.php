<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/** @mixin User */
class UserResource extends JsonResource
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
            'email' => $this->email,
            'roles' => UserRoleResource::collection($this->roles),
            'trashed' => $this->trashed(),
            'avatar' => $this->avatar,
            'links' => $this->when($this->resource->exists, fn() => [
                'view' => route('admin.accounts.view', $this->resource),
                'update' => route('admin.accounts.update', $this->resource),
                'invite' => route('admin.accounts.invite', $this->resource),
            ]),
            'can' => $this->when(! is_null($request->user()), fn() => [
                UserPolicy::VIEW => $request->user()->can(UserPolicy::VIEW, $this->resource),
                UserPolicy::UPDATE => $request->user()->can(UserPolicy::UPDATE, $this->resource),
                UserPolicy::INVITE => $request->user()->can(UserPolicy::INVITE, $this->resource),
                UserPolicy::DELETE => $request->user()->can(UserPolicy::DELETE, $this->resource),
            ], []),
        ];
    }
}
