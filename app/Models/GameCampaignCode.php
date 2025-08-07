<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\GameRound;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GameCampaignCode extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'game_campaigncodes';

    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
    ];

    protected $attributes = [
        'details' => '[]',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<GameSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'game_session_id', 'id');
    }

    /**
     * @return BelongsTo<GameParticipant, $this>
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(GameParticipant::class, 'participant_id', 'id');
    }

    /**
     * @return BelongsTo<ScenarioCampaignCode, $this>
     */
    public function code(): BelongsTo
    {
        return $this->belongsTo(ScenarioCampaignCode::class, 'code_id', 'id');
    }

    /** @return Attribute<GameRound, never> */
    protected function round(): Attribute
    {
        return Attribute::make(
            get: fn() => new GameRound($this->session->scenario, $this->round_id)
        );
    }
}
