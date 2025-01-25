<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Once;
use Symfony\Component\Yaml\Yaml;

class AppPermissionSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permission-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from config to DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            Once::flush();

            $this->syncPermissionsForTenant($tenant);
        });
    }

    private function syncPermissionsForTenant(Tenant $tenant)
    {
        collect($this->getRolesConfig())
            ->each(function ($item) use ($tenant) {
                $role = Role::updateOrCreate([
                    'name' => trim((string) $item['name']),
                ], [
                    'title' => trim((string) $item['title']),
                    'description' => trim($item['description'] ?? ''),
                    'level' => (int) ($item['level'] ?? 0),
                ]);

                $permissions = collect($item['permissions'])
                    ->map(fn($v) => $this->getPermission($tenant, $v));

                $role->syncPermissions($permissions);
            });
    }

    private function getPermission(Tenant $tenant, string $name)
    {
        return once(fn() => Permission::updateOrCreate(['name' => $name]));
    }

    private function getRolesConfig()
    {
        return once(function () {
            $path = resource_path('config/admin/roles.yaml');

            return Yaml::parseFile($path);
        });
    }
}
