<?php

declare(strict_types=1);

use App\Events\TenantCreated;
use App\Models\Tenant;
use App\Support\Multitenancy\DomainTenantFinder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

test('domain tenant finder finds tenant by subdomain', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant = Tenant::create([
        'name' => 'Finder Test',
        'slug' => 'finder-test',
    ]);

    $request = Request::create('https://finder-test.example.com/some/path');
    $finder = new DomainTenantFinder();

    // Act
    $foundTenant = $finder->findForRequest($request);

    // Assert
    expect($foundTenant)->not->toBeNull()
        ->and($foundTenant->id)->toBe($tenant->id);
});

test('domain tenant finder returns null for unknown subdomain', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $request = Request::create('https://unknown-tenant.example.com/some/path');
    $finder = new DomainTenantFinder();

    // Act
    $foundTenant = $finder->findForRequest($request);

    // Assert
    expect($foundTenant)->toBeNull();
});

test('domain tenant finder extracts just the first segment as subdomain', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $tenant = Tenant::create([
        'name' => 'Subdomain Test',
        'slug' => 'subdomain',
    ]);

    $request = Request::create('https://subdomain.multi.level.domain.example.com/path');
    $finder = new DomainTenantFinder();

    // Act
    $foundTenant = $finder->findForRequest($request);

    // Assert
    expect($foundTenant)->not->toBeNull()
        ->and($foundTenant->id)->toBe($tenant->id);
});
