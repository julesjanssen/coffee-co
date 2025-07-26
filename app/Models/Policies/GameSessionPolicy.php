<?php

declare(strict_types=1);

namespace App\Models\Policies;

use App\Models\GameSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GameSessionPolicy
{
    use HandlesAuthorization;

    public const INDEX = 'index';

    public const VIEW = 'view';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public function index(User $user)
    {
        return $user->can('admin.game-sessions.view');
    }

    public function view(User $user, GameSession $session)
    {
        return $user->can('admin.game-sessions.view');
    }

    public function create(User $user)
    {
        return $user->can('admin.game-sessions.create');
    }

    public function update(User $user, GameSession $session)
    {
        if (! $session->exists) {
            return $this->create($user);
        }

        if ($session->trashed()) {
            return false;
        }

        return $user->can('admin.game-sessions.update');
    }

    public function delete(User $user, GameSession $session)
    {
        if ($session->trashed()) {
            return false;
        }

        return $user->can('admin.game-sessions.delete');
    }
}
