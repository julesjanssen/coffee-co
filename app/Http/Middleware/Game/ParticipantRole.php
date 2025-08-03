<?php

declare(strict_types=1);

namespace App\Http\Middleware\Game;

use App\Enums\Participant\Role;
use App\Exceptions\InvalidRoleException;
use Closure;
use Illuminate\Http\Request;

class ParticipantRole
{
    public static function role(Role $role)
    {
        return static::class . ':' . $role->value;
    }

    public static function roles($roles)
    {
        return static::class . ':' . implode(',', array_map(fn($v) => $v->value, $roles));
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param  string  ...$roles
     * @return mixed
     *
     * @throws InvalidRoleException
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if ($request->participant()->role->notIn($roles)) {
            throw new InvalidRoleException('Invalid role.');
        }

        return $next($request);
    }
}
