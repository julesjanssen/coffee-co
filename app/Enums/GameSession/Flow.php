<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Support\Attributes\JsExportable;
use App\Traits\EnumHelpers;

#[JsExportable(name: 'GameSessionFlow')]
enum Flow: string
{
    use EnumHelpers;

    case LOW = 'low';

    case MEDIUM = 'medium';

    case HIGH = 'high';

    public function enableLabConsultingForScore(int $score)
    {
        return match ($this) {
            self::LOW => ($score >= 100),
            self::MEDIUM => ($score > 50),
            self::HIGH => ($score > 0),
        };
    }

    public function mazeIdForScore(int $score = 100)
    {
        return match ($this) {
            self::LOW => 2,
            self::MEDIUM => ($score < 100) ? 2 : 1,
            self::HIGH => ($score < 100) ? 1 : 0,
        };
    }
}
