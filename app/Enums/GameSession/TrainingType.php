<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Traits\EnumHelpers;

enum TrainingType: string
{
    use EnumHelpers;

    case BROAD = 'broad';

    case DEEP = 'deep';

    public function maxCampaigns()
    {
        return match ($this) {
            self::BROAD => 8,
            self::DEEP => 4,
        };
    }

    public function transactionType()
    {
        return match ($this) {
            self::BROAD => TransactionType::MARKETING_TRAINING_BROAD,
            self::DEEP => TransactionType::MARKETING_TRAINING_DEEP,
        };
    }

    public function cost()
    {
        return match ($this) {
            self::BROAD => 40,
            self::DEEP => 40,
        };
    }
}
