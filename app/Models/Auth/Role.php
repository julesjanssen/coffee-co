<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    use UsesTenantConnection;

    protected $attributes = [
        'description' => '',
    ];
}
