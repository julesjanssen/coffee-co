<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected array $connectionsToTransact = [null, 'tenant'];

    protected function refreshTestDatabase()
    {
        // Drop the test tenant database if it exists (to avoid issues with previous test runs)
        Schema::dropDatabaseIfExists('laravel-testing-bluetest');

        // Migrate the landlord database
        $this->artisan('migrate:fresh', [
            '--path' => 'database/migrations/landlord',
            '--database' => 'landlord',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test tenant with a consistent name
        $tenant = Tenant::create(['name' => 'bluetest']);
        $tenant->makeCurrent();

        // Run tenant migrations
        $this->artisan('tenants:artisan', [
            'artisanCommand' => 'migrate',
            '--tenant' => $tenant->id,
        ]);

        // Ensure we clean up after the test
        $this->beforeApplicationDestroyed(function () use ($tenant) {
            Schema::dropDatabaseIfExists($tenant->getDatabaseName());
        });
    }
}
