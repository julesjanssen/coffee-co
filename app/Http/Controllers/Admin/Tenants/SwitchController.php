<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tenants;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SwitchController
{
    public function switch(Request $request, Tenant $tenant)
    {
        Gate::authorize('switch', $tenant);

        $url = vsprintf('%s://%s', [
            $request->getScheme(),
            $tenant->getHost(),
        ]);

        return redirect($url);
    }
}
