<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Traits\EnumHelpers;

enum ScoreType: string
{
    use EnumHelpers;

    case MARKETING_KPI = 'marketing-kpi';

    case MARKETING_TRESHOLD = 'marketing-treshold';

    case NPS = 'nps';

    case MARKETSHARE = 'marketshare';
}
