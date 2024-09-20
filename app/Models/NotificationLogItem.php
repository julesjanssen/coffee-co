<?php

declare(strict_types=1);

namespace App\Models;

use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\NotificationLog\Models\NotificationLogItem as Model;

class NotificationLogItem extends Model
{
    use HasSqids;
    use UsesTenantConnection;
}
