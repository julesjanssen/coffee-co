<?php

declare(strict_types=1);

namespace App\Http\Middleware\Game;

use App\Http\Resources\Game\GameFacilitatorResource;
use App\Http\Resources\Game\GameParticipantResource;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use App\Support\Game\Navigation\Navigation;
use Illuminate\Contracts\Auth\Authenticatable;
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
    protected $rootView = 'game';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        if (file_exists($manifest = public_path('assets/front/manifest.json'))) {
            return hash_file('xxh3', $manifest);
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
                'navigation' => fn() => $this->navigation($request),
                'auth' => fn() => $this->auth($request),
            ],
        ]);
    }

    private function navigation(Request $request)
    {
        $user = $request->user();
        if (! $this->isGameAuthenticatable($user)) {
            return [];
        }

        return Navigation::forAuthenticatable($request, $user);
    }

    private function auth(Request $request)
    {
        $user = $request->user();
        if (! $this->isGameAuthenticatable($user)) {
            return [];
        }

        if ($user instanceof GameParticipant) {
            return GameParticipantResource::make($user);
        }

        return GameFacilitatorResource::make($user);
    }

    private function isGameAuthenticatable(?Authenticatable $user = null)
    {
        if (is_null($user)) {
            return false;
        }

        return ($user instanceof GameParticipant || $user instanceof GameFacilitator);
    }
}
