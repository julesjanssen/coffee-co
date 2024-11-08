<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Cache::forever('system:scheduler:timestamp', time());
})->everyMinute();

Schedule::command('app:backup')->dailyAt('04:00');
Schedule::command('app:backup:clean')->dailyAt('04:30');
