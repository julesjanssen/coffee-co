<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Login;
use App\Models\User;
use App\Support\Login\IpInfo;
use App\Support\Login\UserAgent;
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
        /** @var User $authenticatable */
        $authenticatable = $this->authenticatable;

        return [
            'sqid' => $this->sqid,
            'authenticatable' => [
                'type' => $this->authenticatable_type,
                'name' => $authenticatable->name,
            ],
            'createdAt' => $this->created_at,
            'ip' => IpInfo::getDetails($this->ip),
            'userAgent' => UserAgent::getDetails($this->user_agent),
            'links' => [
                'view' => route('admin.accounts.view', [$authenticatable]),
            ],
        ];
    }
}
