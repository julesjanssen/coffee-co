<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tenants;

use App\Http\Resources\Admin\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UpdateController
{
    public function update(Request $request, Tenant $tenant)
    {
        Gate::authorize('update', $tenant);

        return Inertia::render('tenants/update', [
            'tenant' => fn() => TenantResource::make($tenant),
        ]);
    }

    public function store(Request $request, Tenant $tenant)
    {
        Gate::authorize('update', $tenant);

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $tenant->fill([
            'name' => $request->input('name'),
        ])->save();

        return redirect()->route('admin.tenants.view', [$tenant]);
    }
}
