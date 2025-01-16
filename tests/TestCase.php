<?php

declare(strict_types=1);

namespace Tests;

use App\Actions\TenantCreate;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected array $connectionsToTransact = [null, 'tenant'];

    protected function refreshTestDatabase()
    {
        $this->artisan('migrate:fresh', [
            '--path' => 'database/migrations/landlord',
            '--database' => 'landlord',
        ]);
    }

    protected function setUp(): void
    {
        parent::setup();

        $tenant = TenantCreate::forceRun('testing');
        $tenant->makeCurrent();

        $this->beforeApplicationDestroyed(function () use ($tenant) {
            Schema::dropDatabaseIfExists($tenant->getDatabaseName());
        });
    }
}
