<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Resources\Admin\UserResource;
use App\Models\Auth\Role;
use App\Models\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        Gate::authorize('index', User::class);

        $user = $request->user();

        $invisibleRoles = Role::query()
            ->where('level', '>', $user->roles()->max('level'))
            ->get();

        $accounts = User::query()
            ->with([
                'roles' => fn($q) => $q->orderByDesc('level'),
            ])
            ->withoutRole($invisibleRoles)
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('accounts/index', [
            'accounts' => UserResource::collection($accounts)
                ->additional([
                    'can' => [
                        UserPolicy::CREATE => $request->user()->can(UserPolicy::CREATE, User::class),
                    ],
                    'links' => [
                        'create' => route('admin.accounts.create'),
                    ],
                ]),
        ]);
    }
}
