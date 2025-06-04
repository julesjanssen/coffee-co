<?php

declare(strict_types=1);

namespace App\Listeners\Cleanup;

use App\Events\AppCleaningUp;
use App\Models\Tenant;
use App\Models\TusUpload;
use Illuminate\Support\Facades\Date;

class TusCleanup
{
    public function handle(AppCleaningUp $event)
    {
        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            TusUpload::query()
                ->where('expires_at', '<', Date::parse('-6 hours'))
                ->get()
                ->each(function ($upload) {
                    $this->removeUpload($upload);
                });
        });
    }

    private function removeUpload(TusUpload $upload)
    {
        $path = $upload->path;
        if (file_exists($path)) {
            @unlink($path);
        }

        $upload->delete();
    }
}
