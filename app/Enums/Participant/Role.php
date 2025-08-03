<?php

declare(strict_types=1);

namespace App\Enums\Participant;

use App\Traits\EnumHelpers;
use Illuminate\Support\Str;

enum Role: string
{
    use EnumHelpers;

    case SALES_1 = 'sales-1';

    case SALES_2 = 'sales-2';

    case SALES_3 = 'sales-3';

    case SALES_SCREEN = 'sales-screen';

    case TECHNICAL_1 = 'technical-1';

    case TECHNICAL_2 = 'technical-2';

    case TECHNICAL_SCREEN = 'technical-screen';

    case MARKETING_1 = 'marketing-1';

    case BACKOFFICE_1 = 'backoffice-1';

    case MATERIALS_1 = 'materials-1';

    public function description(): string
    {
        return match ($this) {
            self::SALES_1 => __('participants-role:sales-1'),
            self::SALES_2 => __('participants-role:sales-2'),
            self::SALES_3 => __('participants-role:sales-3'),
            self::SALES_SCREEN => __('participants-role:sales-screen'),
            self::TECHNICAL_1 => __('participants-role:technical-1'),
            self::TECHNICAL_2 => __('participants-role:technical-2'),
            self::TECHNICAL_SCREEN => __('participants-role:technical-screen'),
            self::MARKETING_1 => __('participants-role:marketing-1'),
            self::BACKOFFICE_1 => __('participants-role:backoffice-1'),
            self::MATERIALS_1 => __('participants-role:material-1'),
        };
    }

    public function playerID(): int
    {
        return (int) Str::afterLast($this->value, '-') ?: 1;
    }
}
