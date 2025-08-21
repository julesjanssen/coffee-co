<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SystemTask;
use App\Models\Tenant;
use App\Support\SystemTasks\ImportScenario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

use function Laravel\Prompts\select;

class AppScenarioImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scenario-import';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Tenant::find(1)->makeCurrent();

        $options = Config::collection('coffeeco.scenario_groups', [])
            ->keyBy('base_id')
            ->map(fn($v) => sprintf('%s (%s)', $v['title'], $v['locale']))
            ->toArray();

        $baseID = select(
            label: 'Select scenario',
            options: $options,
        );

        SystemTask::dispatch(new ImportScenario(), ['baseID' => $baseID]);

        $this->info('Scenario import scheduled.');

        return self::SUCCESS;
    }
}
