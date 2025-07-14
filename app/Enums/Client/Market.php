<?php

declare(strict_types=1);

namespace App\Enums\Client;

use App\Traits\EnumHelpers;

enum Market: string
{
    use EnumHelpers;

    case HOTEL = 'hotel';

    case RESTAURANT = 'restaurant';

    case COFFEEBAR = 'coffee-bar';

    case CAFE = 'cafe';

    case CAFETARIA = 'cafetaria';
}
