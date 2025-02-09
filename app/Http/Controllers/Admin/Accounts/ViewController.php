<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Resources\Admin\LoginResource;
use App\Http\Resources\Admin\NotificationLogItemResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Login;
use App\Models\NotificationLogItem;
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
            'notifications' => Inertia::optional(fn() => $this->listNotifications($account)),
            'links' => [
                'index' => route('admin.accounts.index'),
            ],
        ]);
    }

    private function listLogins(User $account)
    {
        $results = Login::query()
            ->with('authenticatable')
            ->whereMorphedTo('authenticatable', $account)
            ->where('success', '=', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return LoginResource::collection($results);
    }

    private function listNotifications(User $account)
    {
        $results = NotificationLogItem::query()
            ->whereMorphedTo('notifiable', $account)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return NotificationLogItemResource::collection($results);
    }
}
