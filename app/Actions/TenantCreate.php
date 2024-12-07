<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Multitenancy\Concerns\UsesMultitenancyConfig;

class TenantCreate
{
    use UsesMultitenancyConfig;

    public static function run(...$arguments)
    {
        return (new self())->handle(...$arguments);
    }

    public static function forceRun(string $name)
    {
        $slug = Str::slug($name);

        return (new self())->createAndPrepare($name, $slug);
    }

    public function handle(string $name, ?string $slug = null)
    {
        if (Str::length($name) > 100) {
            throw new InvalidArgumentException('Tenant name should be shorter than 100 chars.');
        }

        if (empty($slug)) {
            $slug = Str::slug($name);
        }

        $slug = substr($slug, 0, 30);

        $exists = Tenant::query()
            ->where('slug', '=', $slug)
            ->first();

        if ($exists) {
            throw new InvalidArgumentException("Tenant with slug {$slug} already exists.");
        }

        if (! $this->isValidSubdomain($slug)) {
            throw new InvalidArgumentException("The name '{$name}' is not allowed ('{$slug}' appears on blocklist).");
        }

        $this->createAndPrepare($name, $slug);
    }

    private function createAndPrepare(string $name, string $slug)
    {
        $tenant = Tenant::create([
            'name' => $name,
            'slug' => $slug,
            'settings' => [
                'storage-prefix' => $this->createStoragePrefix(),
            ],
        ]);

        $this->createDatabase($tenant);
        $this->migrateDatabase($tenant);
        $this->syncPermissions($tenant);

        return $tenant;
    }

    private function createStoragePrefix()
    {
        while (true) {
            $prefix = Str::lower(Str::random(10));

            $exists = Tenant::query()
                ->where('settings->storage-prefix', '=', $prefix)
                ->exists();

            if (! $exists) {
                return $prefix;
            }
        }
    }

    private function createDatabase(Tenant $tenant)
    {
        Schema::createDatabase($tenant->getDatabaseName());
    }

    private function migrateDatabase(Tenant $tenant)
    {
        $command = vsprintf('tenants:artisan "migrate --database=tenant --path=%s" --tenant=%d', [
            'database/migrations/tenant',
            $tenant->id,
        ]);

        Artisan::call($command);
    }

    private function syncPermissions(Tenant $tenant)
    {
        Artisan::call('app:permission-sync');
    }

    private function isValidSubdomain($subdomain)
    {
        return ! $this->getSubdomainBlocklist()->contains($subdomain);
    }

    private function getSubdomainBlocklist()
    {
        return LazyCollection::make(function () {
            $path = resource_path('config/subdomain-blocklist.txt');
            $handle = fopen($path, 'r');

            while (($line = fgets($handle)) !== false) {
                yield trim($line);
            }
        });
    }
}
