<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Support\Attributes\JsExportable;
use App\Traits\EnumHelpers;

#[JsExportable(name: 'GameSessionRoundStatus')]
enum RoundStatus: string
{
    use EnumHelpers;

    case ACTIVE = 'active';

    case PROCESSING = 'processing';

    case PROCESSED = 'processed';

    case PAUSED = 'paused';
}
