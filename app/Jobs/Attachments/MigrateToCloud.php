<?php

declare(strict_types=1);

namespace App\Jobs\Attachments;

use App\Enums\AttachmentType;
use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class MigrateToCloud implements NotTenantAware, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Attachment::query()
            ->whereIn('type', [
                AttachmentType::ScenarioClientImage,
            ])
            ->whereIn('disk', ['public', 'local', 's3', 'scenario'])
            ->where('status', '=', 'done')
            ->orderBy('id')
            ->limit(100)
            ->get()
            ->each(fn($v) => MoveToCloud::dispatch($v));
    }
}
