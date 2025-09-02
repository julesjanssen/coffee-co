<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Scenarios;

use App\Enums\Scenario\Status;
use App\Http\Resources\Admin\ScenarioResource;
use App\Models\Scenario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        Gate::authorize('admin.scenarios.view');

        $user = $request->user();

        return Inertia::render('scenarios/index', [
            'results' => $this->listScenarios(),
            'groups' => $this->listScenarioGroups(),
            'can' => [
                'update' => $user->can('admin.scenarios.update'),
            ],
        ]);
    }

    private function listScenarios()
    {
        /** @phpstan-ignore method.notFound */
        $scenarios = Scenario::query()
            ->withExpression('scenario_groups', function ($query) {
                $query->from('scenarios', 's')
                    ->select([
                        's.*',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY group_id ORDER BY id DESC) as g'),
                    ]);
            })
            ->from('scenario_groups')
            ->where('g', '=', 1)
            ->whereIn('status', [
                Status::ACTIVE,
                Status::PROCESSING,
            ])
            ->get();

        return ScenarioResource::collection($scenarios);
    }

    private function listScenarioGroups()
    {
        return Config::collection('coffeeco.scenario_groups')
            ->map(fn($c) => [
                'baseID' => $c['base_id'],
                'title' => $c['title'],
                'locale' => $c['locale']->toArray(),
                'links' => [
                    'sync' => route('admin.scenarios.sync', ['group' => $c['base_id']]),
                ],
            ]);
    }
}
