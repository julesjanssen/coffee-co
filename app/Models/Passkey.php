<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\LaravelPasskeys\Models\Passkey as Base;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Passkey extends Base
{
    use UsesTenantConnection;
}
