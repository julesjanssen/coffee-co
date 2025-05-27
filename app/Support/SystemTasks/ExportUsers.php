<?php

declare(strict_types=1);

namespace App\Support\SystemTasks;

use App\Models\SystemTask;
use App\Support\SystemTasks\Contracts\SystemTaskRunner;
use Illuminate\Support\Sleep;

class ExportUsers implements SystemTaskRunner
{
    public function handle(SystemTask $task)
    {
        Sleep::for(4)->seconds();

        $filename = date('Y-m-d_His') . '.csv';
        $task->storage()->put($filename, "id,name\n1,Alice\n2,Bob");

        $task->update([
            'result' => [
                ...$task->result,
                'filename' => $filename,
            ],
        ]);
    }
}
