<?php

declare(strict_types=1);

namespace App\Enums\Product;

use App\Traits\EnumHelpers;

enum Material: string
{
    use EnumHelpers;

    case ALUMINIUM = 'aluminium';

    case CHROME = 'chrome';

    case IMPOVERISHED_URANIUM = 'impoverished-uranium';

    case LIGHTWEIGHT_METAL = 'lightweight-metal';

    case STAINLESS_STEEL = 'stainless-steel';

    case TITANIUM = 'titanium';

    case UNOBTAINIUM = 'unobtainium';
}
