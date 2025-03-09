<?php

declare(strict_types=1);

use App\Enums\Disk;
use App\Events\TenantCreated;
use App\Models\Tenant;
use App\Support\Multitenancy\SetTenantStorage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

test('tenant storage task configures all tenant disks', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant = Tenant::create([
        'name' => 'Multi Disk Test',
        'slug' => 'multi-disk',
    ]);

    $storageTask = new SetTenantStorage();

    // Act
    $storageTask->makeCurrent($tenant);

    // Assert
    expect(Storage::disk(Disk::TENANT->value))->not->toBeNull()
        ->and(Storage::disk(Disk::TENANT_CLOUD->value))->not->toBeNull()
        ->and(Storage::disk(Disk::TENANT_BACKUP->value))->not->toBeNull();
});
