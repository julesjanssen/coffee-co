<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tenants;

use App\Http\Resources\Admin\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, Tenant $tenant)
    {
        Gate::authorize('view', $tenant);

        return Inertia::render('tenants/view', [
            'tenant' => TenantResource::make($tenant),
        ]);
    }
}
