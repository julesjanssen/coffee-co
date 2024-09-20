<?php

declare(strict_types=1);

namespace App\Support\Multitenancy;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class DomainTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $slug = strtok($host, '.');

        /** @var Tenant */
        return app(IsTenant::class)::where('slug', '=', $slug)->first();
    }
}
