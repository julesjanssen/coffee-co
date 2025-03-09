<?php

declare(strict_types=1);

use App\Events\TenantCreated;
use App\Events\TenantCreating;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;

test('a tenant can be created with basic attributes', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $attributes = [
        'name' => 'Test Tenant',
        'slug' => 'test-tenant',
    ];

    // Act
    $tenant = Tenant::create($attributes);

    // Assert
    expect($tenant)->toBeInstanceOf(Tenant::class)
        ->and($tenant->name)->toBe('Test Tenant')
        ->and($tenant->slug)->toBe('test-tenant')
        ->and($tenant->settings)->toBeArray()
        ->and($tenant->settings)->toHaveKey('storage-prefix');
});

test('tenant creation dispatches creating and created events', function () {
    // Arrange
    Event::fake([TenantCreating::class, TenantCreated::class]);

    $attributes = [
        'name' => 'Event Test Tenant',
        'slug' => 'event-test',
    ];

    // Act
    $tenant = Tenant::create($attributes);

    // Assert
    Event::assertDispatched(TenantCreating::class, function ($event) use ($tenant) {
        return $event->tenant->is($tenant);
    });

    Event::assertDispatched(TenantCreated::class, function ($event) use ($tenant) {
        return $event->tenant->is($tenant);
    });
});

test('tenant is created with a unique storage prefix', function () {
    Event::fake([TenantCreated::class]);

    // Act
    $tenant1 = Tenant::create([
        'name' => 'Tenant One',
        'slug' => 'tenant-one',
    ]);

    $tenant2 = Tenant::create([
        'name' => 'Tenant Two',
        'slug' => 'tenant-two',
    ]);

    // Assert
    expect($tenant1->settings['storage-prefix'])->not->toBe($tenant2->settings['storage-prefix'])
        ->and(strlen($tenant1->settings['storage-prefix']))->toBe(10)
        ->and(strlen($tenant2->settings['storage-prefix']))->toBe(10);
});

test('tenant slugs must be unique', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    Tenant::create([
        'name' => 'First Tenant',
        'slug' => 'duplicate-slug',
    ]);

    // Act & Assert
    expect(fn() => Tenant::create([
        'name' => 'Second Tenant',
        'slug' => 'duplicate-slug',
    ]))->toThrow(Exception::class);
});
