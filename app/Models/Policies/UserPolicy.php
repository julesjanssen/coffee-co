<?php

declare(strict_types=1);

namespace App\Models\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public const INDEX = 'index';

    public const VIEW = 'view';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public const INVITE = 'invite';

    public function index(User $user)
    {
        return $user->can('admin.accounts.view');
    }

    public function view(User $user, User $account)
    {
        return $user->can('admin.accounts.view');
    }

    public function create(User $user)
    {
        return $user->can('admin.accounts.create');
    }

    public function update(User $user, User $account)
    {
        if (! $account->exists) {
            return $this->create($user);
        }

        if ($account->trashed()) {
            return false;
        }

        if ($user->maxRoleLevel < $account->maxRoleLevel) {
            return false;
        }

        return $user->can('admin.accounts.update');
    }

    public function delete(User $user, User $account)
    {
        if ($user->is($account)) {
            return false;
        }

        if ($account->trashed()) {
            return false;
        }

        if ($user->maxRoleLevel < $account->maxRoleLevel) {
            return false;
        }

        return $user->can('admin.accounts.delete');
    }

    public function invite(User $user, User $account)
    {
        return empty($account->password) && $user->can('admin.accounts.invite');
    }
}
