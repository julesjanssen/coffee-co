<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Tenant;
use Illuminate\Console\Command;
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
        if (Tenant::count() === 0) {
            return;
        }

        Tenant::all()->eachCurrent(function (Tenant $tenant) {
            $this->syncPermissionsForTenant($tenant);
        });
    }

    private function syncPermissionsForTenant(Tenant $tenant)
    {
        collect($this->getRolesConfig())
            ->each(function ($item) {
                $role = Role::updateOrCreate([
                    'name' => $item['name'],
                ], [
                    'title' => $item['title'],
                    'description' => $item['description'] ?? '',
                ]);

                $permissions = collect($item['permissions'])
                    ->map(fn($v) => $this->getPermission($v));

                $role->syncPermissions($permissions);
            });
    }

    private function getPermission(string $name)
    {
        return once(function () use ($name) {
            return Permission::updateOrCreate(['name' => $name]);
        });
    }

    private function getRolesConfig()
    {
        return once(function () {
            $path = resource_path('config/admin/roles.yaml');

            return Yaml::parseFile($path);
        });
    }
}
