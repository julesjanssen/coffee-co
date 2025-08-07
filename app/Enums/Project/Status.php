<?php

declare(strict_types=1);

namespace App\Enums\Project;

use App\Support\Attributes\JsExportable;
use App\Traits\EnumHelpers;

#[JsExportable(name: 'ProjectStatus')]
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
