<?php

declare(strict_types=1);

namespace App\Http\Middleware\Game;

use App\Http\Resources\Game\GameSessionResource;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Closure;
use Illuminate\Http\Request;
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

        Inertia::share([
            'session' => GameSessionResource::make($user->session),
        ]);

        return $next($request);
    }
}
