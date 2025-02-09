<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UserRoleResource;
use App\Models\Auth\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UpdateController
{
    public function update(Request $request, User $account)
    {
        Gate::authorize('update', $account);

        $user = $request->user();

        return Inertia::render('accounts/update', [
            'account' => fn() => UserResource::make($account),
            'roles' => fn() => UserRoleResource::collection($this->rolesForUser($user)),
            'details' => fn() => $this->getDetailsFromEmail($request),
        ]);
    }

    public function store(Request $request, User $account)
    {
        Gate::authorize('update', $account);

        $user = $request->user();
        $roles = $this->rolesForUser($user)->pluck('name');

        $emailRule = Rule::unique('tenant.users', 'email')->withoutTrashed();
        if ($account->exists) {
            $emailRule->ignore($account);
        }

        $request->validate([
            'name' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email', $emailRule],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::in($roles)],
        ]);

        $account->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        $account->syncRoles($request->input('roles', []));
        $account->save();

        return redirect()->route('admin.accounts.view', [$account]);
    }

    private function rolesForUser(User $user)
    {
        return Role::query()
            ->where('level', '<=', $user->maxRoleLevel)
            ->orderBy('level', 'desc')
            ->get();
    }

    private function getDetailsFromEmail(Request $request)
    {
        $email = $request->input('email');
        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $details = Cache::rememberForever(__METHOD__ . ':' . $email, function () use ($email) {
            $hash = hash('sha256', strtolower((string) $email));
            $url = sprintf('https://www.gravatar.com/%s.json', $hash);

            try {
                $response = Http::asJson()->throw()->get($url);
            } catch (Exception) {
                return;
            }

            $json = $response->json();
            $first = Arr::first(Arr::get($json, 'entry', []), null, []);

            return [
                'name' => Arr::get($first, 'name.formatted', Arr::get($first, 'displayName')),
            ];
        });

        return $details;
    }
}
