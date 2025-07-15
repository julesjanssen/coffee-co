<?php

declare(strict_types=1);

namespace App\Enums\GameSession;

use App\Traits\EnumHelpers;

enum Status: string
{
    use EnumHelpers;

    case PENDING = 'pending';

    case PLAYING = 'playing';

    case FINISHED = 'finished';
}
