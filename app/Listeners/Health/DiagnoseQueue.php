<?php

declare(strict_types=1);

namespace App\Listeners\Health;

use App\Exceptions\HealthException;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnoseQueue
{
    public function handle(DiagnosingHealth $event)
    {
        $queue = app()->make('queue');
        $driver = $queue->getName();

        if ($driver !== 'database') {
            return;
        }

        if (! Schema::hasTable('jobs')) {
            return;
        }

        //
        // check for jobs older than 1 hour
        //
        $jobs = DB::table('jobs')
            ->where('available_at', '<', time() - 3600)
            ->count();

        if ($jobs > 0) {
            throw new HealthException('Queue: has ' . number_format($jobs, 0) . ' jobs older than 1 hour.');
        }
    }
}
