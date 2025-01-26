<?php

declare(strict_types=1);

namespace App\Models\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public const INDEX = 'index';

    public const VIEW = 'view';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public const SWITCH = 'switch';

    public function index(User $user)
    {
        return $user->can('admin.tenants.view');
    }

    public function view(User $user, Tenant $tenant)
    {
        return $user->can('admin.tenants.view');
    }

    public function create(User $user)
    {
        return $user->can('admin.tenants.create');
    }

    public function update(User $user, Tenant $tenant)
    {
        if (! $tenant->exists) {
            return $this->create($user);
        }

        return $user->can('admin.tenants.update');
    }

    public function delete(User $user, Tenant $tenant)
    {
        if (Tenant::current()->is($tenant)) {
            return false;
        }

        return $user->can('admin.tenants.delete');
    }

    public function switch(User $user, Tenant $tenant)
    {
        return $user->can('admin.tenants.switch');
    }
}
