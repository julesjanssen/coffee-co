<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\GameRound;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GameMmmaActivation extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'game_mmma_activations';

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

    /** @return Attribute<GameRound, never> */
    protected function round(): Attribute
    {
        return Attribute::make(
            get: fn() => new GameRound($this->session->scenario, $this->round_id)
        );
    }

    public static function isActiveForSession(GameSession $session)
    {
        /** @phpstan-ignore method.notFound */
        $result = self::query()
            ->withExpression('activation_periods', function ($query) use ($session) {
                $currentRoundID = $session->currentRound->roundID;
                $mmmaEnabledRoundCount = $session->settings->mmmaEnabledRoundCount;
                $minRoundID = ($currentRoundID - $session->settings->mmmaEffectiveRoundCount - ($mmmaEnabledRoundCount - 1));

                $query->from('game_mmma_activations')
                    ->select(['round_id'])
                    ->selectRaw('round_id + ? as end_round', [($mmmaEnabledRoundCount - 1)])
                    ->selectRaw('LEAD(round_id) OVER (PARTITION BY game_session_id ORDER BY round_id) as next_activation')
                    ->where('game_session_id', '=', $session->id)
                    ->where('round_id', '>=', $minRoundID);
            })
            ->withExpression('gaps', function ($query) {
                $query
                    ->from('activation_periods')
                    ->select([
                        'round_id',
                        'end_round',
                    ])
                    ->selectRaw('CASE
                      WHEN next_activation IS NOT NULL AND next_activation > end_round + 1
                      THEN next_activation - end_round - 1
                      ELSE 0
                    END as gap_size');
            })
            ->selectRaw('MAX(gap_size) as max_gap_size')
            ->selectRaw('MIN(round_id) as min_round_id')
            ->selectRaw('MAX(end_round) as max_round_id')
            ->from('gaps')
            ->toBase()
            ->first();

        $gapSize = (int) $result->max_gap_size;

        return $gapSize === 0
            && $result->min_round_id <= ($session->currentRound->roundID - $session->settings->mmmaEffectiveRoundCount)
            && $result->max_round_id >= ($session->currentRound->roundID);
    }
}
