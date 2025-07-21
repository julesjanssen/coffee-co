<?php

declare(strict_types=1);

namespace App\Enums\Project;

use App\Traits\EnumHelpers;

enum Status: string
{
    use EnumHelpers;

    case PENDING = 'pending';

    case LOST = 'lost';

    case WON = 'won';

    case ACTIVE = 'active';

    case DOWN = 'down';

    case FINISHED = 'finished';
}
