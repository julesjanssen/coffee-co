<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Resources\Admin\LoginResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Login;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, User $account)
    {
        Gate::authorize('view', $account);

        return Inertia::render('accounts/view', [
            'account' => UserResource::make($account),
            'logins' => Inertia::defer(fn() => $this->listLogins($account)),
        ]);
    }

    private function listLogins(User $account)
    {
        $results = Login::query()
            ->whereMorphedTo('authenticatable', $account)
            ->with('authenticatable')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return LoginResource::collection($results);
    }
}
