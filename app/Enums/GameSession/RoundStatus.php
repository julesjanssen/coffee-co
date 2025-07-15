<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Traits\EnumHelpers;

enum RoundStatus: string
{
    use EnumHelpers;

    case ACTIVE = 'active';

    case PROCESSING = 'processing';

    case PAUSED = 'paused';
}
