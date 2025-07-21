<?php

declare(strict_types=1);

namespace App\Enums\Scenario;

use App\Traits\EnumHelpers;

enum Status: string
{
    use EnumHelpers;

    case PROCESSING = 'processing';

    case DRAFT = 'draft';

    case ACTIVE = 'active';

    case ARCHIVED = 'archived';
}
