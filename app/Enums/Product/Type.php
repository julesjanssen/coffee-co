<?php

declare(strict_types=1);

namespace App\Enums\Product;

use App\Traits\EnumHelpers;

enum Type: string
{
    use EnumHelpers;

    case BEAN_GRINDER = 'bean-grinder';

    case CAPPUCCINO_COFFEE_MACHINE = 'cappuccino-coffee-machine';

    case CAPPUCCINO_MACHINE = 'cappuccino-machine';

    case CHOCOLATE_STEAMER = 'chocolate-steamer';

    case CHOCOLATE_STEAMER_CREAM_WHIPPER = 'chocolate-steamer-cream-whipper';

    case COFFEE_MACHINE = 'coffee-machine';

    case CONVEYER_BELT = 'conveyer-belt';

    case CREAM_WHIPPER = 'cream-whipper';

    case CUP_WARMER = 'cup-warmer';

    case ICE_CRUNCHER = 'ice-cruncher';

    case MILK_STEAMER = 'milk-steamer';

    case WATER_BOILER = 'water-boiler';
}
