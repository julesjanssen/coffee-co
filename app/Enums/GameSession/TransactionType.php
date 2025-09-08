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

    public function description(): string
    {
        return match ($this) {
            self::LAB_CONSULTING => __('transaction-type:lab-consulting'),
            self::MARKETING_CAMPAIGN => __('transaction-type:marketing-campaign'),
            self::MARKETING_TRAINING_BROAD => __('transaction-type:marketing-training-broad'),
            self::MARKETING_TRAINING_DEEP => __('transaction-type:marketing-training-deep'),
            self::HDMA => __('transaction-type:hdma'),
            self::OPERATIONAL_COST => __('transaction-type:operational-cost'),
            self::PROJECT_UPTIME_BONUS => __('transaction-type:project-uptime-bonus'),
            self::PROJECT_WON => __('transaction-type:project-won'),
        };
    }

    public function dbAlias()
    {
        return hash('xxh3', $this->value);
    }
}
