<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\GameRound;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GameHdmaActivation extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'game_hdma_activations';

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

    public static function getContinuouslyActiveRounds(GameSession $session)
    {
        if ($session->isPending()) {
            return 0;
        }

        /** @phpstan-ignore method.notFound */
        $result = self::query()
            ->withExpression('activation_periods', function ($query) use ($session) {
                $hdmaEnabledRoundCount = $session->settings->hdmaEnabledRoundCount;

                $query
                    ->from('game_hdma_activations')
                    ->select(['round_id'])
                    ->selectRaw('round_id + ? as end_round', [($hdmaEnabledRoundCount - 1)])
                    ->where('game_session_id', '=', $session->id)
                    ->orderBy('round_id');
            })
            ->withExpression('gaps', function ($query) {
                $query
                    ->from('activation_periods')
                    ->select([
                        'round_id',
                        'end_round',
                    ])
                    ->selectRaw('LAG(end_round) OVER (ORDER BY round_id) as prev_end_round')
                    ->selectRaw('CASE
                        WHEN LAG(end_round) OVER (ORDER BY round_id) IS NULL
                        OR round_id <= LAG(end_round) OVER (ORDER BY round_id) + 1
                          THEN 0
                          ELSE 1
                        END as has_gap');
            })
            ->withExpression('sequence_groups', function ($query) {
                $query
                    ->from('gaps')
                    ->select([
                        'round_id',
                        'end_round',
                    ])
                    ->selectRaw('SUM(has_gap) OVER (ORDER BY round_id) as sequence_group');
            })
            ->withExpression('sequences', function ($query) {
                $query
                    ->from('sequence_groups')
                    ->select('sequence_group')
                    ->selectRaw('MIN(round_id) as seq_start')
                    ->selectRaw('MAX(end_round) as seq_end')
                    ->groupBy('sequence_group');
            })
            ->from('sequences')
            ->selectRaw('COALESCE(? - seq_start + 1, 0) as continuously_active_rounds', [
                $session->currentRound->roundID,
            ])
            ->where('seq_end', '>=', $session->currentRound->roundID)
            ->orderBy('seq_start', 'desc')
            ->toBase()
            ->value('continuously_active_rounds');

        return (int) $result;
    }

    public static function isActiveForSession(GameSession $session)
    {
        $activeRounds = self::getContinuouslyActiveRounds($session);

        return $activeRounds >= $session->settings->hdmaEffectiveRoundCount;
    }
}
