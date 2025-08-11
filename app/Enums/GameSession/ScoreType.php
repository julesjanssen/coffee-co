<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Traits\EnumHelpers;

enum ScoreType: string
{
    use EnumHelpers;

    case MARKETING = 'marketing';

    case NPS = 'nps';

    case MARKETSHARE = 'marketshare';
}
