<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\Status;
use App\Events\GameSessionCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'round_status' => RoundStatus::class,
        'status' => Status::class,
        'settings' => 'array',
        'started_at' => 'timestamp',
        'finished_at' => 'timestamp',
    ];

    protected $attributes = [
        'current_round_id' => 0,
        'status' => Status::PENDING,
        'settings' => '[]',
    ];

    protected $dispatchesEvents = [
        'created' => GameSessionCreated::class,
    ];

    /**
     * @return HasMany<GameParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(GameParticipant::class);
    }

    /**
     * @return HasOne<GameFacilitator, $this>
     */
    public function facilitator(): HasOne
    {
        return $this->hasOne(GameFacilitator::class);
    }
}
