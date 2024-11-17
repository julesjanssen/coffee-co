<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;

enum AttachmentType: string
{
    use EnumHelpers;

    case Avatar = 'avatar';

    case Generic = 'generic';
}
