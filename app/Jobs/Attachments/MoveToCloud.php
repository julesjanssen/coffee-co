<?php

declare(strict_types=1);

namespace App\Jobs\Attachments;

use App\Enums\Disk;
use App\Enums\Queue;
use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class MoveToCloud implements NotTenantAware, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Attachment $attachment,
    ) {
        $this->onQueue(Queue::FILE_PROCESSING);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (App::isLocal()) {
            return;
        }

        $this->attachment->moveToDisk(Disk::TENANT_CLOUD->value);
    }
}
