<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tenants;

use App\Http\Resources\Admin\TenantResource;
use App\Models\Policies\TenantPolicy;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        Gate::authorize('index', Tenant::class);

        $tenants = Tenant::query()
            ->orderBy('name')
            ->paginate(30);

        return Inertia::render('tenants/index', [
            'tenants' => TenantResource::collection($tenants)
                ->additional([
                    'can' => [
                        TenantPolicy::CREATE => $request->user()->can(TenantPolicy::CREATE, User::class),
                    ],
                    'links' => [
                        'create' => route('admin.tenants.create'),
                    ],
                ]),
        ]);
    }
}
