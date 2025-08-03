<?php

declare(strict_types=1);

namespace App\Enums\Client;

use App\Traits\EnumHelpers;

enum YearsInBusiness: string
{
    use EnumHelpers;

    case Y2 = 'y2';

    case Y4 = 'y4';

    case Y6 = 'y6';

    case Y8 = 'y8';

    case Y10 = 'y10';

    public function description(): string
    {
        return match ($this) {
            self::Y2 => __(':years years', ['years' => 2]),
            self::Y4 => __(':years years', ['years' => 4]),
            self::Y6 => __(':years years', ['years' => 6]),
            self::Y8 => __(':years years', ['years' => 8]),
            self::Y10 => __(':years years', ['years' => 10]),
        };
    }

    public static function fromInteger(int $years)
    {
        return self::from('y' . $years);
    }
}
