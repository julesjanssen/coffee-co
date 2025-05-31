<?php

declare(strict_types=1);

namespace App\Enums;

use App\Support\Attributes\JsExportable;

#[JsExportable()]
enum SystemTaskStatus: string
{
    case PENDING = 'pending';

    case PROCESSING = 'processing';

    case COMPLETED = 'completed';

    case FAILED = 'failed';
}
