<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\Status;
use App\Events\GameSessionCreated;
use App\Models\Traits\Reservable;
use App\Values\GameRound;
use App\Values\GameSessionSettings;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GameSession extends Model
{
    use HasSqids;
    use Reservable;
    use SoftDeletes;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'current_round_id' => 'integer',
        'round_status' => RoundStatus::class,
        'status' => Status::class,
        'settings' => GameSessionSettings::class,
        'started_at' => 'timestamp',
        'finished_at' => 'timestamp',
    ];

    protected $attributes = [
        'current_round_id' => 0,
        'round_status' => RoundStatus::PAUSED,
        'status' => Status::PENDING,
        'settings' => '[]',
    ];

    protected $dispatchesEvents = [
        'created' => GameSessionCreated::class,
    ];

    public static function booted()
    {
        self::creating(function ($model) {
            if (empty($model->public_id)) {
                while (true) {
                    $model->public_id = Str::lower(Str::random());
                    $exists = self::query()
                        ->where('public_id', '=', $model->public_id)
                        ->exists();

                    if (! $exists) {
                        break;
                    }
                }
            }
        });
    }

    /**
     * @return BelongsTo<Scenario, $this>
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id', 'id');
    }

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

    /**
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->chaperone('session');
    }

    /** @return Attribute<GameRound | null, never> */
    protected function currentRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->scenario_id) ? null : new GameRound($this->scenario, $this->current_round_id)
        )->shouldCache();
    }

    public function pause()
    {
        $this->settings->shouldPauseAfterCurrentRound = false;
        $this->round_status = RoundStatus::PAUSED;
        $this->save();
    }

    public function isMMMAActive()
    {
        return ($this->mmmaLevel() > 12);
    }

    public function mmmaLevel()
    {
        // TODO: implement calc
        return 0;
    }

    public function netPromotorScore()
    {
        return $this->scenario->clients
            ->map(fn($client) => $client->netPromotorScoreForGameSession($this))
            ->avg();
    }

    public function marketingTresholdScore()
    {
        return 10;
    }

    public function pickRelevantScenario(): ?Scenario
    {
        if (! empty($this->scenario_id)) {
            return $this->scenario;
        }

        return Scenario::query()
            ->where('group_id', '=', $this->scenario_group_id)
            ->whereActive()
            ->orderBy('id', 'desc')
            ->first();
    }

    public function topicUrl()
    {
        return url('/topics/' . hash('xxh3', 'session:' . $this->id));
    }
}
