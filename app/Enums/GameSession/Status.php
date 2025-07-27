<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Support\Attributes\JsExportable;
use App\Traits\EnumHelpers;

#[JsExportable(name: 'GameSessionStatus')]
enum Status: string
{
    use EnumHelpers;

    case PENDING = 'pending';

    case PLAYING = 'playing';

    case FINISHED = 'finished';

    case CLOSED = 'closed';
}
