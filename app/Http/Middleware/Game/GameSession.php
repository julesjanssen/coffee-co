<?php

declare(strict_types=1);

namespace App\Http\Middleware\Game;

use App\Http\Resources\Game\GameSessionResource;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;

class GameSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var GameParticipant|GameFacilitator|null $user */
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $this->ensureRouteIsAccessible($request, $user);

        $scenario = $user->session->scenario;
        if (empty($scenario)) {
            $scenario = $user->session->pickRelevantScenario();
        }

        App::setLocale($scenario->locale->value);

        Inertia::share([
            'session' => GameSessionResource::make($user->session),
        ]);

        return $next($request);
    }

    private function ensureRouteIsAccessible(Request $request, GameParticipant|GameFacilitator $user)
    {
        if ($user instanceof GameFacilitator) {
            return;
        }

        if ($user->session->isPending()) {
            abort(redirect()->route('game.pending'));
        }

        // during pause, automatically redirect to a allowed route
        if ($user->session->isPaused()) {
            if ($user->role->isActiveDuringBreak()) {
                $pauseRoutes = $user->role->pauseRoutes();
                if (! empty($pauseRoutes)) {
                    if (! in_array($request->url(), $pauseRoutes)) {
                        abort(redirect($pauseRoutes[0]));
                    }
                }
            }
        }
    }
}
