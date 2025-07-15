<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\GameSessionCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GameSession extends Model
{
    use HasSqids;
    use SoftDeletes;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'current_round_id' => 'integer',
        'settings' => 'array',
        'started_at' => 'timestamp',
        'finished_at' => 'timestamp',
    ];

    protected $attributes = [
        'current_round_id' => 0,
        'status' => 'pending',
        'settings' => '[]',
    ];

    protected $dispatchesEvents = [
        'created' => GameSessionCreated::class,
    ];
}
