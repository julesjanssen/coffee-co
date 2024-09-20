<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Artisan;

class AppMigrate extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->migrateLandlord();
        $this->migrateTenants();
        $this->migrateSessions();
        $this->migrateCache();
    }

    private function migrateLandlord()
    {
        $this->info('Migrate landlord');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/landlord',
            '--database' => 'landlord',
        ], $this->output);
    }

    private function migrateTenants()
    {
        if (Tenant::count() === 0) {
            return;
        }

        $this->info('Migrate tenants');

        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            $command = vsprintf('tenants:artisan "migrate --database=tenant --path=%s" --tenant=%d', [
                'database/migrations/tenant',
                $tenant->id,
            ]);

            Artisan::call($command, [], $this->output);
        });
    }

    private function migrateSessions()
    {
        $path = config('database.connections.sessions.database');
        if (! file_exists($path)) {
            touch($path);
        }

        $this->info('Migrate sessions');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/sessions',
            '--database' => 'sessions',
        ], $this->output);
    }

    private function migrateCache()
    {
        $path = config('database.connections.cache.database');
        if (! file_exists($path)) {
            touch($path);
        }

        $this->info('Migrate cache');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/cache',
            '--database' => 'cache',
        ], $this->output);
    }
}
