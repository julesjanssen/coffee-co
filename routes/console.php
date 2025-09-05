<?php

declare(strict_types=1);

use App\Jobs\CloseStaleGameSessions;
use App\Jobs\PruneGameSessions;
use App\Jobs\PruneScenarios;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Cache::forever('system:scheduler:timestamp', time());
})->everyMinute();

Schedule::command('app:backup')->dailyAt('04:00');
Schedule::command('app:backup:clean')->dailyAt('04:30');
Schedule::command('app:cleanup')->dailyAt('05:00');

Schedule::job(CloseStaleGameSessions::class)->dailyAt('08:00');
Schedule::job(PruneGameSessions::class)->dailyAt('08:00');
Schedule::job(PruneScenarios::class)->dailyAt('08:00');
