<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SystemTaskStatus;
use App\Models\SystemTask;
use App\Support\SystemTasks\Contracts\SystemTaskRunner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExecuteSystemTask implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @param class-string<SystemTaskRunner> $runnerClass */
    public function __construct(
        public SystemTask $task,
        public string $runnerClass,
    ) {}

    public function handle()
    {
        $task = $this->task;
        $task->setStatus(SystemTaskStatus::PROCESSING);

        try {
            $runner = app($this->runnerClass);
            $runner->handle($task);

            $task->setStatus(SystemTaskStatus::COMPLETED);
        } catch (Throwable $e) {
            $task->update([
                'status' => SystemTaskStatus::FAILED,
                'result' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }
}
