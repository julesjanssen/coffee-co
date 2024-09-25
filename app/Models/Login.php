<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Login extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'success' => 'boolean',
        'details' => 'array',
    ];

    protected $attributes = [
        'details' => '[]',
    ];

    public const UPDATED_AT = null;

    public function authenticatable(): MorphTo
    {
        return $this->morphTo('authenticatable');
    }
}
