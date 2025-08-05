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

    public function years(): int
    {
        return match ($this) {
            self::Y2 => 2,
            self::Y4 => 4,
            self::Y6 => 6,
            self::Y8 => 8,
            self::Y10 => 10,
        };
    }

    public function description(): string
    {
        return __(':years years', ['years' => $this->years()]);
    }

    public static function fromInteger(int $years)
    {
        return self::from('y' . $years);
    }
}
