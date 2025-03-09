<?php

declare(strict_types=1);

use App\Events\TenantCreated;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;

test('can make a tenant current', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant = Tenant::create([
        'name' => 'Current Test',
        'slug' => 'current-test',
    ]);

    // Act
    $tenant->makeCurrent();

    // Assert
    expect(Tenant::current())->not->toBeNull()
        ->and(Tenant::current()->id)->toBe($tenant->id);
});

test('can check if a tenant is current', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant = Tenant::create([
        'name' => 'Is Current Test',
        'slug' => 'is-current-test',
    ]);

    // Initial state - not current
    expect($tenant->isCurrent())->toBeFalse();

    // Act
    $tenant->makeCurrent();

    // Assert
    expect($tenant->isCurrent())->toBeTrue();
});

test('can forget current tenant', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant = Tenant::create([
        'name' => 'Forget Test',
        'slug' => 'forget-test',
    ]);

    $tenant->makeCurrent();
    expect(Tenant::current())->not->toBeNull();

    // Act
    Tenant::forgetCurrent();

    // Assert
    expect(Tenant::current())->toBeNull();
});

test('can switch between tenants', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant1 = Tenant::create([
        'name' => 'First Tenant',
        'slug' => 'first-tenant',
    ]);

    $tenant2 = Tenant::create([
        'name' => 'Second Tenant',
        'slug' => 'second-tenant',
    ]);

    // Act & Assert - Switch to first tenant
    $tenant1->makeCurrent();
    expect(Tenant::current()->id)->toBe($tenant1->id);

    // Act & Assert - Switch to second tenant
    $tenant2->makeCurrent();
    expect(Tenant::current()->id)->toBe($tenant2->id);
});
