<?php

declare(strict_types=1);

namespace App\Listeners\Cleanup;

use App\Events\AppCleaningUp;
use App\Models\SystemTask;
use App\Models\Tenant;
use Illuminate\Support\Facades\Date;

class SystemTaskCleanup
{
    public function handle(AppCleaningUp $event)
    {
        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            SystemTask::query()
                ->where('expires_at', '<', Date::parse('-6 hours'))
                ->get()
                ->each(function ($task) {
                    $this->removeTask($task);
                });
        });
    }

    private function removeTask(SystemTask $task)
    {
        $filename = $task->result['filename'] ?? null;
        if (! empty($filename)) {
            $task->storage()->delete($filename);
        }

        $task->delete();
    }
}
