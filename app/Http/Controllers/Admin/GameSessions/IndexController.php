<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\GameSessions;

use App\Http\Resources\Admin\GameSessionIndexResource;
use App\Models\GameSession;
use App\Models\Policies\GameSessionPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        Gate::authorize(GameSessionPolicy::INDEX, GameSession::class);

        /** @phpstan-ignore method.notFound */
        $results = GameSession::query()
            ->with(['scenario'])
            ->withExpression('scenario_groups', function ($query) {
                $query->from('scenarios', 's')
                    ->select([
                        's.*',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY group_id ORDER BY id DESC) as g'),
                    ]);
            })
            ->select('game_sessions.*')
            ->selectRaw('CASE WHEN game_sessions.scenario_id IS NULL THEN sg.title ELSE s.title END AS scenario_title')
            ->selectRaw('CASE WHEN game_sessions.scenario_id IS NULL THEN sg.locale ELSE s.locale END AS scenario_locale')
            ->leftJoin('scenarios as s', fn($q) => $q->on('s.id', '=', 'game_sessions.scenario_id'))
            ->leftJoin('scenario_groups as sg', function ($query) {
                $query
                    ->on('sg.group_id', '=', 'game_sessions.scenario_group_id')
                    ->where('sg.g', '=', 1);
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(20)
            ->withQueryString();

        return Inertia::render('game-sessions/index', [
            'results' => GameSessionIndexResource::collection($results)
                ->additional([
                    'can' => [
                        GameSessionPolicy::CREATE => $request->user()->can(GameSessionPolicy::CREATE, GameSession::class),
                    ],
                    'links' => [
                        'create' => route('admin.game-sessions.create'),
                    ],
                ]),
        ]);
    }
}
