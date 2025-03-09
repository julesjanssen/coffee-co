<?php

declare(strict_types=1);

use App\Events\TenantCreated;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;

test('tenant generates correct subdomain-based host', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $appUrl = config('app.url');
    $baseHost = parse_url($appUrl, PHP_URL_HOST);

    $tenant = Tenant::create([
        'name' => 'Domain Test',
        'slug' => 'domain-test',
    ]);

    // Act
    $host = $tenant->getHost();

    // Assert
    expect($host)->toBe("domain-test.$baseHost");
});

test('tenant with dot in slug uses slug as host directly', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $fullDomain = 'custom.domain.example';

    $tenant = Tenant::create([
        'name' => 'Full Domain Test',
        'slug' => $fullDomain,
    ]);

    // Act
    $host = $tenant->getHost();

    // Assert
    expect($host)->toBe($fullDomain);
});

test('tenant generates correct database name', function () {
    Event::fake([TenantCreated::class]);

    // Arrange
    $landlordDb = config('database.connections.landlord.database');

    $tenant = Tenant::create([
        'name' => 'Database Test',
        'slug' => 'database-test',
    ]);

    // Act
    $dbName = $tenant->getDatabaseName();

    // Assert
    expect($dbName)->toBe("$landlordDb-database-test");
});
