<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Config\Settings;

use App\Http\Resources\Admin\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IndexController
{
    public function index(Request $request)
    {
        sleep(2);

        return [
            'tenants' => $this->listTenants($request),
        ];
    }

    private function listTenants(Request $request)
    {
        $tenant = Tenant::current();
        $user = $request->user();

        if ($user->cannot('admin.tenants.switch')) {
            return [TenantResource::make($tenant)];
        }

        $tenants = Tenant::query()
            ->orderBy('name')
            ->get();

        return TenantResource::collection($tenants);
    }
}
