<?php

declare(strict_types=1);

namespace App\Http\Middleware\Admin;

use App\Support\Admin\Navigation;
use Illuminate\Http\Request;
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
        if (str_starts_with($request->path(), 'admin')) {
            if (file_exists($manifest = public_path('assets/admin/manifest.json'))) {
                return hash_file('xxh3', $manifest);
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
                'account' => fn() => $this->account($request),
            ],
        ]);
    }

    private function navigation(Request $request)
    {
        return Navigation::build($request);
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
