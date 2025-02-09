<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class InviteController
{
    public function store(Request $request, User $account)
    {
        Gate::authorize('invite', $account);

        $account->notify(new AccountInvitation());

        return response(null, Response::HTTP_ACCEPTED);
    }
}
