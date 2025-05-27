<?php

declare(strict_types=1);

namespace App\Support\SystemTasks\Contracts;

use App\Models\SystemTask;

interface SystemTaskRunner
{
    public function handle(SystemTask $task);
}
