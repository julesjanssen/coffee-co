<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Scenarios;

use App\Models\SystemTask;
use App\Support\SystemTasks\ImportScenario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class SyncController
{
    public function store(Request $request, string $baseID)
    {
        Gate::authorize('admin.scenarios.update');

        $config = Config::collection('coffeeco.scenario_groups', [])
            ->firstWhere('base_id', '=', $baseID);

        if (empty($config)) {
            throw new RuntimeException('Invalid baseID / config.');
        }

        $task = SystemTask::dispatch(new ImportScenario(), ['baseID' => $baseID]);

        return $task;
    }
}
