<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Vagebond\Tus\Models\Upload;

class TusUpload extends Upload
{
    use UsesTenantConnection;

    public function getPathAttribute()
    {
        $tenantID = Tenant::current()?->id ?? 0;

        return storage_path('uploads/' . $tenantID . '/' . $this->getBasenameAttribute());
    }
}
