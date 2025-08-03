<?php

declare(strict_types=1);

namespace App\Enums\Project;

use App\Traits\EnumHelpers;

enum Location: string
{
    use EnumHelpers;

    case AMSTERDAM = 'amsterdam';

    case BERLIN = 'berlin';

    case COPENHAGEN = 'copenhagen';

    case DUBLIN = 'dublin';

    case LONDON = 'london';

    case PARIS = 'paris';
}
