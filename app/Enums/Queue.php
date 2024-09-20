<?php

declare(strict_types=1);

namespace App\Enums;

enum Queue: string
{
    case DEFAULT = 'default';

    case FILE_PROCESSING = 'file-processing';

    case MAIL_BROADCAST = 'mail-broadcast';

    case ERRORS = 'errors';
}
