<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Support\Attributes\JsExportable;
use App\Traits\EnumHelpers;

#[JsExportable(name: 'TransactionType')]
enum TransactionType: string
{
    use EnumHelpers;

    case LAB_CONSULTING = 'lab-consulting';

    case MARKETING_CAMPAIGN = 'marketing-campaign';

    case MARKETING_TRAINING_BROAD = 'marketing-training-broad';

    case MARKETING_TRAINING_DEEP = 'marketing-training-deep';

    case HDMA = 'hdma';

    case OPERATIONAL_COST = 'operational-cost';

    case PROJECT_UPTIME_BONUS = 'project-uptime-bonus';

    case PROJECT_WON = 'project-won';

    public function dbAlias()
    {
        return hash('xxh3', $this->value);
    }
}
