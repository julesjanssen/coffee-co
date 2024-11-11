<?php

declare(strict_types=1);

namespace App\Support\Multitenancy;

use App\Enums\Disk;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SetTenantStorage implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        /** @var Tenant $tenant */
        $prefix = vsprintf('/%s/', [
            $tenant->settings['storage-prefix'],
        ]);

        Storage::set(Disk::TENANT->value, Storage::build([
            'driver' => 'scoped',
            'disk' => 's3',
            'prefix' => $prefix,
        ]));

        Storage::set(Disk::TENANT_BACKUP->value, Storage::build([
            'driver' => 'scoped',
            'disk' => 'backup',
            'prefix' => $prefix,
        ]));
    }

    public function forgetCurrent(): void
    {
        Storage::forgetDisk(['tenant', 'tenant-backup']);
    }
}
