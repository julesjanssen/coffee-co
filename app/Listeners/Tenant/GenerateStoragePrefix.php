<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\TenantCreating;
use App\Models\Tenant;
use Illuminate\Support\Str;

class GenerateStoragePrefix
{
    public function handle(TenantCreating $event)
    {
        /** @var Tenant $tenant */
        $tenant = $event->tenant;

        $tenant->settings = [
            ...$tenant->settings,
            'storage-prefix' => $this->createStoragePrefix(),
        ];
    }

    private function createStoragePrefix()
    {
        while (true) {
            $prefix = Str::lower(Str::random(10));

            $exists = Tenant::query()
                ->where('settings->storage-prefix', '=', $prefix)
                ->exists();

            if (! $exists) {
                return $prefix;
            }
        }
    }
}
