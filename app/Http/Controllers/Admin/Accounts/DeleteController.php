<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeleteController
{
    public function delete(Request $request, User $account)
    {
        Gate::authorize('delete', $account);

        $account->delete();

        return response()->noContent();
    }
}
