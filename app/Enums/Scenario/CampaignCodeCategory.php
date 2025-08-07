<?php

declare(strict_types=1);

namespace App\Enums\Scenario;

use App\Traits\EnumHelpers;

enum CampaignCodeCategory: string
{
    use EnumHelpers;

    case EASY = 'easy';

    case DIFFICULT = 'difficult';

    public function kpiScore()
    {
        return match ($this) {
            self::EASY => 1,
            self::DIFFICULT => 1,
        };
    }

    public function tresholdScore()
    {
        return match ($this) {
            self::EASY => 0,
            self::DIFFICULT => 1,
        };
    }

    public function cost()
    {
        return match ($this) {
            self::EASY => 10,
            self::DIFFICULT => 50,
        };
    }
}
