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

        // during pause, automatically redirect to a fixed route
        if ($user instanceof GameParticipant && $user->session->isPaused()) {
            $role = $user->role;
            if ($role->isActiveDuringBreak()) {
                $pauseRoute = $role->pauseRoute();
                if (! empty($pauseRoute)) {
                    if ($request->url() !== $pauseRoute) {
                        return redirect($pauseRoute);
                    }
                }
            }
        }

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
}
