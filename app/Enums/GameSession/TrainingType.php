<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Traits\EnumHelpers;

enum TrainingType: string
{
    use EnumHelpers;

    case BROAD = 'broad';

    case DEEP = 'deep';
}
