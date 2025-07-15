<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Participant\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GameParticipant extends Authenticatable
{
    use HasSqids;
    use Notifiable;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'role' => Role::class,
    ];

    protected $attributes = [];

    public $timestamps = false;

    /**
     * @return BelongsTo<GameSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'game_session_id', 'id');
    }
}
