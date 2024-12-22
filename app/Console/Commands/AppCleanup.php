<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\AppCleaningUp;
use App\Models\Login;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Finder\Finder;

class AppCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up obsolete files & database records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->cleanTmpDir();
        $this->purgeExpiredPasswordResets();
        $this->cleanUserLogins();
        $this->cleanLogsDir();
        $this->removeEmptyStorageDirs();

        Event::dispatch(new AppCleaningUp());
    }

    protected function cleanTmpDir()
    {
        $path = storage_path('tmp');

        if (! file_exists($path)) {
            return;
        }

        $finder = new Finder();
        $finder->files()
            ->in($path)
            ->date('before 12 hours ago');

        foreach ($finder as $file) {
            unlink($file->getPathname());
        }
    }

    private function purgeExpiredPasswordResets()
    {
        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            Artisan::call('auth:clear-resets', [], $this->output);
        });
    }

    private function cleanUserLogins()
    {
        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            Login::query()
                ->where('created_at', '<', Date::parse('12 months ago'))
                ->delete();
        });
    }

    private function cleanLogsDir()
    {
        $path = dirname(config('logging.channels.daily.path'));

        $finder = new Finder();
        $finder->files()
            ->in($path)
            ->date('before 2 weeks ago');

        foreach ($finder as $file) {
            unlink($file->getPathname());
        }
    }

    protected function removeEmptyStorageDirs()
    {
        $dir = storage_path('app');
        if (! file_exists($dir)) {
            return;
        }

        $command = [
            'find',
            escapeshellarg($dir),
            '-empty',
            '-type d',
            '-delete',
        ];

        Process::run(implode(' ', $command))->throw();
    }
}
