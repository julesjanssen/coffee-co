<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeController
{
    public function update(Request $request)
    {
        $account = $request->user();
        $account->load(['roles']);

        return Inertia::render('accounts/me', [
            'account' => UserResource::make($account),
        ]);
    }

    public function store(Request $request)
    {
        $account = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $account->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.account.me.update');
    }
}
