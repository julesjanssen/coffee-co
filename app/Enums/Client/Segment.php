<?php

declare(strict_types=1);

namespace App\Enums\Client;

use App\Traits\EnumHelpers;

enum Segment: string
{
    use EnumHelpers;

    case TECHNICAL_SPECS = 'technical-specs';

    case EFFICIENCY = 'efficiency';

    case TOTAL_SOLUTION = 'total-solution';
}
