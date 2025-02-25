<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\ArrayInput;

class AppModelPrune extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:model:prune
                                {--model=* : Class names of the models to be pruned}
                                {--except=* : Class names of the models to be excluded from pruning}
                                {--path=* : Absolute path(s) to directories where models are located}
                                {--chunk=1000 : The number of models to retrieve per chunk of models to be deleted}
                                {--pretend : Display the number of prunable records found instead of deleting them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune models that are no longer needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Tenant::count() === 0) {
            return;
        }

        $options = collect($this->options())
            ->filter()
            ->mapWithKeys(fn($v, $k) => ['--' . $k => $v === true ? null : $v])
            ->toArray();

        Tenant::all()->eachCurrent(function (Tenant $tenant) use ($options) {
            $input = new ArrayInput($options);
            $command = sprintf('tenants:artisan "model:prune %s" --tenant=%d', (string) $input, $tenant->id);

            Artisan::call($command, [], $this->output);
        });
    }
}
