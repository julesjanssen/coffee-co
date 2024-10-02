<?php

declare(strict_types=1);

namespace App\Http\Middleware\Admin;

use App\Models\Tenant;
use App\Support\Admin\Navigation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'admin';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        if (Str::startsWith($request->path(), ['admin'])) {
            if (file_exists($manifest = public_path('assets/admin/manifest.json'))) {
                return md5_file($manifest);
            }
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'app' => [
                'env' => config('app.env'),
                'title' => config('app.title'),
                'route' => route('admin.dashboard.index'),
                'navigation' => fn() => $this->navigation($request),
                'tenant' => fn() => $this->tenant($request),
                'account' => fn() => $this->account($request),
            ],
        ]);
    }

    private function navigation(Request $request)
    {
        return Navigation::build($request);
    }

    private function tenant(Request $request)
    {
        $tenant = Tenant::current();
        if (empty($tenant)) {
            return;
        }

        return [
            'title' => $tenant->name,
        ];
    }

    private function account(Request $request)
    {
        if (! $user = $request->user()) {
            return [];
        }

        return [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ];
    }
}
