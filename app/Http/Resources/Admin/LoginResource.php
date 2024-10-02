<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Login;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Login */
class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $authenticatable = $this->authenticatable;

        if ($authenticatable instanceof User) {
            $viewLink = route('admin.accounts.view', [$authenticatable]);
        } else {
            $viewLink = null;
        }

        return [
            'sqid' => $this->sqid,
            'authenticatable' => [
                'type' => $this->authenticatable_type,
                'name' => $authenticatable->name,
            ],
            'createdAt' => $this->created_at,
            'ip' => $this->ip,
            'userAgent' => $this->user_agent,
            'links' => [
                'view' => $viewLink,
            ],
        ];
    }
}
