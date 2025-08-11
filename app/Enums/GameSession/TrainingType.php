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
}
