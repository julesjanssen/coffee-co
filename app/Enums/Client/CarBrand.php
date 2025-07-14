<?php

declare(strict_types=1);

namespace App\Enums\Client;

use App\Traits\EnumHelpers;

enum CarBrand: string
{
    use EnumHelpers;

    case AUDI = 'audi';

    case MERCEDES = 'mercedes';

    case VOLKSWAGEN = 'volkswagen';

    case SKODA = 'skoda';

    case RENAULT = 'renault';
}
