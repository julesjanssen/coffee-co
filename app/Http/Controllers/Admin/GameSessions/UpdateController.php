<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\GameSessions;

use App\Http\Resources\Admin\GameSessionUpdateResource;
use App\Models\GameSession;
use App\Models\Policies\GameSessionPolicy;
use App\Models\Scenario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UpdateController
{
    public function update(Request $request, GameSession $session)
    {
        Gate::authorize(GameSessionPolicy::UPDATE, $session);

        return Inertia::render('game-sessions/update', [
            'session' => fn() => GameSessionUpdateResource::make($session),
            'scenarios' => $this->listScenarios($request, $session),
        ]);
    }

    public function store(Request $request, GameSession $session)
    {
        Gate::authorize(GameSessionPolicy::UPDATE, $session);

        $scenarios = $this->listScenarios($request, $session);

        $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:100'],
            'scenario' => ['required', 'string', Rule::in($scenarios->keys())],
        ]);

        $scenarioGroupID = $scenarios->get($request->input('scenario'))->groupID;

        $session->fill([
            'scenario_group_id' => $scenarioGroupID,
            'title' => $request->input('title'),
        ]);

        $session->save();

        return redirect()->route('admin.game-sessions.view', ['session' => $session]);
    }

    private function listScenarios(Request $request, GameSession $session)
    {
        if ($session->exists) {
            return collect([]);
        }

        return Scenario::query()
            ->whereActive()
            ->orderBy('title')
            ->get()
            ->keyBy('sqid')
            ->map(fn($v) => (object) [
                'sqid' => $v->sqid,
                'groupID' => $v->group_id,
                'title' => $v->title,
            ]);
    }
}
