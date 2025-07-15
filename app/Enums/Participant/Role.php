<?php

declare(strict_types=1);

namespace App\Enums\Participant;

use App\Traits\EnumHelpers;

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
}
