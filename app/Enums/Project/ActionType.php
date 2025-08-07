<?php

declare(strict_types=1);

namespace App\Enums\Project;

use App\Traits\EnumHelpers;

enum ActionType: string
{
    use EnumHelpers;

    case INSTALLATION = 'installation';

    case MAINTENANCE = 'maintenance';

    case FIX = 'fix';
}
