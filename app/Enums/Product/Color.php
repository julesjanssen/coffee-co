<?php

declare(strict_types=1);

namespace App\Enums\Product;

use App\Traits\EnumHelpers;

enum Color: string
{
    use EnumHelpers;

    case BLACK = 'black';

    case BLUE = 'blue';

    case BROWN = 'brown';

    case GREEN = 'green';

    case ORANGE = 'orange';

    case PURPLE = 'purple';

    case RED = 'red';

    case WHITE = 'white';

    case YELLOW = 'yellow';
}
