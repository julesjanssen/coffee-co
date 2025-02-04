<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tenants;

use App\Actions\TenantCreate;
use App\Http\Resources\Admin\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use InvalidArgumentException;

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
            'name' => ['required', 'min:2', 'max:100'],
        ]);

        if ($tenant->exists) {
            $tenant->update([
                'name' => $request->input('name'),
            ]);
        } else {
            $tenant = $this->createNewTenant($request);
        }

        return redirect()->route('admin.tenants.view', [$tenant]);
    }

    private function createNewTenant(Request $request)
    {
        try {
            return app(TenantCreate::class)->run(...$request->all());
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'name' => [$e->getMessage()],
            ]);
        }
    }
}
