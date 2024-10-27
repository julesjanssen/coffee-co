<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttachmentType;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Vagebond\Bijlagen\Enums\AttachmentStatus;
use Vagebond\Bijlagen\Models\Attachment as Model;

class Attachment extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected function casts(): array
    {
        return [
            'type' => AttachmentType::class,
            'data' => 'array',
            'status' => AttachmentStatus::class,
        ];
    }
}
