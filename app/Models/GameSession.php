<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GameSession\Flow;
use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\ScoreType;
use App\Enums\GameSession\Status;
use App\Enums\Project\Status as ProjectStatus;
use App\Events\GameSessionCreated;
use App\Models\Traits\Reservable;
use App\Support\Random\GameSessionRandomizer;
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

    /**
     * @return HasMany<GameTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(GameTransaction::class)->chaperone('session');
    }

    /**
     * @return HasMany<GameScore, $this>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(GameScore::class)->chaperone('session');
    }

    /** @return Attribute<GameRound | null, never> */
    protected function currentRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->scenario_id) ? null : new GameRound($this->scenario, $this->current_round_id)
        );
    }

    /** @return Attribute<int | null, never> */
    protected function currentYear(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->currentRound?->year()
        );
    }

    /** @return Attribute<Flow, never> */
    protected function flow(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->settings->flow
        );
    }

    public function randomizer()
    {
        return GameSessionRandomizer::forGameSession($this);
    }

    public function isHDMAActive()
    {
        return GameHdmaActivation::isActiveForSession($this);
    }

    public function canRefreshHdma()
    {
        if ($this->isPending()) {
            return false;
        }

        $cooldown = $this->settings->hdmaRefreshRoundCooldown;

        $exists = GameHdmaActivation::query()
            ->where('game_session_id', '=', $this->id)
            ->where('round_id', '>', $this->currentRound->addRounds(-1 * $cooldown)->roundID)
            ->exists();

        return ! $exists;
    }

    public function averageUptime()
    {
        $result = ProjectHistoryItem::query()
            ->join('projects as p', function ($query) {
                $query->on('p.id', '=', 'project_history.project_id');
            })
            /** @phpstan-ignore argument.type */
            ->where('p.game_session_id', '=', $this->id)
            ->whereIn('project_history.status', [
                ProjectStatus::ACTIVE,
                ProjectStatus::DOWN,
            ])
            ->selectAverageUptime()
            ->toBase()
            ->first();

        return (float) $result->avg_uptime;
    }

    public function netPromotorScoreForClient(ScenarioClient $client)
    {
        $npsDelta = $this->scores()
            ->where('client_id', '=', $client->id)
            ->where('type', '=', ScoreType::NPS)
            ->avg('value');

        return max(0, min(100, $this->settings->clientNpsStart + $npsDelta));
    }

    public function netPromotorScorePerClient()
    {
        return $this->scenario->clients()
            ->leftJoin('game_scores as gs', function ($q) {
                $q->on('gs.client_id', '=', 'scenario_clients.id');
            })
            ->where(function ($q) {
                $q
                    /** @phpstan-ignore argument.type */
                    ->where('gs.type', '=', ScoreType::NPS)
                    ->orWhereNull('gs.type');
            })
            ->select([
                'scenario_clients.id',
                'scenario_clients.title',
            ])
            ->selectRaw('SUM(gs.value) as nps_delta')
            ->groupBy(['scenario_clients.id', 'scenario_clients.title'])
            ->get()
            ->map(function ($client) {
                /** @phpstan-ignore property.notFound */
                $value = max(0, min(100, $this->settings->clientNpsStart + $client->nps_delta));

                return [
                    'sqid' => $client->sqid,
                    'title' => $client->title,
                    'nps' => $value,
                ];
            });
    }

    public function marketingTresholdScore()
    {
        return $this->scores()
            ->where('type', '=', ScoreType::MARKETING_TRESHOLD)
            ->sum('value');
    }

    public function marketingKpiScore()
    {
        return $this->scores()
            ->where('type', '=', ScoreType::MARKETING_KPI)
            ->sum('value');
    }

    public function canDisplayResults()
    {
        if ($this->isPending()) {
            return false;
        }

        if ($this->currentRound->isFirstRound()) {
            return false;
        }

        return true;
    }

    public function pause()
    {
        $this->settings->shouldPauseAfterCurrentRound = false;
        $this->round_status = RoundStatus::PAUSED;
        $this->save();
    }

    public function isPaused()
    {
        return $this->round_status->is(RoundStatus::PAUSED);
    }

    public function isPending()
    {
        return $this->status->is(Status::PENDING);
    }

    public function close()
    {
        $this->update([
            'status' => Status::CLOSED,
        ]);
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
