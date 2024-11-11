<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

enum Disk: string
{
    use EnumHelpers;

    case TENANT = 'tenant';

    case TENANT_BACKUP = 'tenant-backup';

    public function storage(): FilesystemAdapter
    {
        return Storage::disk($this->value);
    }
}
