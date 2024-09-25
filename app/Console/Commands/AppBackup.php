<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Spatie\Backup\Config\Config;

class AppBackup extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run backups for landlord and all tenants.';

    /**
     * Execute the console command.
     */
    public function handle(Config $config)
    {
        $this->backupLandlord($config);
        $this->backupTenants($config);
    }

    private function backupLandlord(Config $config)
    {
        $config->backup->source->databases = ['landlord'];

        Tenant::first()->makeCurrent();

        Artisan::call('backup:run', [
            '--filename' => sprintf('%s-%s.zip', $this->getFilenameTimestamp(), 'landlord'),
        ], $this->output);
    }

    private function backupTenants(Config $config)
    {
        if (Tenant::count() === 0) {
            return;
        }

        Tenant::all()->eachCurrent(function (Tenant $tenant) use ($config) {
            $config->backup->source->databases = ['tenant'];

            Artisan::call('backup:run', [
                '--filename' => sprintf('%s-%04d-%s.zip', $this->getFilenameTimestamp(), $tenant->id, $tenant->slug),
            ], $this->output);
        });
    }

    private function getFilenameTimestamp(): string
    {
        return Date::now()->format('Ymdhi');
    }
}
