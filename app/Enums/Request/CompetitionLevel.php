<?php

declare(strict_types=1);

namespace App\Enums\Request;

use App\Traits\EnumHelpers;

enum CompetitionLevel: string
{
    use EnumHelpers;

    case LOW = 'low';

    case MEDIUM = 'medium';

    case HIGH = 'high';
}
