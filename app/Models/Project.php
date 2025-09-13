<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Project\Location;
use App\Enums\Project\Status;
use App\Events\ProjectUpdated;
use App\Values\GameRound;
use App\Values\GameYear;
use App\Values\ProjectSettings;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Project extends Model
{
    use HasFactory;
    use HasSqids;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'price' => 'int',
        'failure_chance' => 'int',
        'downtime' => 'int',
        'location' => Location::class,
        'settings' => ProjectSettings::class,
    ];

    protected $attributes = [
        'status' => Status::PENDING,
        'failure_chance' => 0,
        'downtime' => 0,
        'settings' => '[]',
    ];

    protected $dispatchesEvents = [
        'updated' => ProjectUpdated::class,
    ];

    /**
     * @return BelongsTo<GameSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'game_session_id', 'id');
    }

    /**
     * @return BelongsTo<ScenarioRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(ScenarioRequest::class, 'request_id', 'id');
    }

    /**
     * @return BelongsTo<ScenarioRequestSolution, $this>
     */
    public function solution(): BelongsTo
    {
        return $this->belongsTo(ScenarioRequestSolution::class, 'solution_id', 'id');
    }

    /**
     * @return BelongsTo<ScenarioClient, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ScenarioClient::class, 'client_id', 'id');
    }

    /**
     * @return HasMany<ProjectAction, $this>
     */
    public function actions(): HasMany
    {
        return $this->hasMany(ProjectAction::class, 'project_id', 'id');
    }

    /**
     * @return HasMany<ProjectHistoryItem, $this>
     */
    public function historyItems(): HasMany
    {
        return $this->hasMany(ProjectHistoryItem::class, 'project_id', 'id');
    }

    #[Scope]
    protected function whereRequestedInYear(Builder $query, GameYear $year)
    {
        $query->whereIn('request_round_id', $year->roundIDs());
    }

    #[Scope]
    protected function whereClient(Builder $query, ScenarioClient $client)
    {
        $query->where('client_id', '=', $client->id);
    }

    #[Scope]
    protected function filterAndOrderByStatus(Builder $query, Collection|array $statuses, string $direction = 'asc')
    {
        $statuses = collect($statuses)
            ->map(fn($v) => Status::coerce($v))
            ->map(fn($v) => $v->value);

        $expr = vsprintf('FIELD(status, %s) %s', [
            str_repeat('?,', $statuses->count() - 1) . '?',
            $direction,
        ]);

        $query
            ->whereIn('status', $statuses)
            ->orderByRaw($expr, $statuses->toArray());
    }

    /** @return Attribute<string, never> */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status->in([Status::PENDING, Status::LOST])) {
                    return $this->request->title;
                }

                return $this->solution->products()->map(fn($v) => $v->title)->join(', ');
            }
        )->shouldCache();
    }

    /** @return Attribute<GameRound | null, never> */
    protected function requestRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->request_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->request_round_id),
        );
    }

    /** @return Attribute<GameRound | null, never> */
    protected function quoteBeforeRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->request_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->request_round_id + $this->session->settings->roundsToSubmitOffer),
        );
    }

    /** @return Attribute<GameRound | null, never> */
    protected function quoteRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->quote_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->quote_round_id),
        );
    }

    /** @return Attribute<GameRound | null, never> */
    protected function deliverBeforeRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->quote_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->quote_round_id + $this->session->settings->roundsToDeliverProject),
        );
    }

    /** @return Attribute<GameRound | null, never> */
    protected function deliveryRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->delivery_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->delivery_round_id),
        );
    }

    /** @return Attribute<GameRound | null, never> */
    protected function downRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->down_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->down_round_id),
        );
    }

    /** @return Attribute<GameRound | null, never> */
    protected function endOfContractRound(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->delivery_round_id)
                ? null
                : new GameRound($this->session->scenario, $this->delivery_round_id + $this->request->duration),
        );
    }

    public function shouldBeQuotedBy()
    {
        $requestedRoundID = $this->request_round_id;
        $roundsToSubmitOffer = $this->session->settings->roundsToSubmitOffer;

        return new GameRound($this->session->scenario, ($requestedRoundID + $roundsToSubmitOffer));
    }

    public function uptimePercentage()
    {
        $uptime = ($this->request->duration - $this->downtime);

        return (($uptime / $this->request->duration) * 100);
    }

    public function currentStateHash(string $salt = '')
    {
        return hash('xxh3', implode(':', [
            $this->id,
            $this->updated_at,
            $salt,
        ]));
    }

    public static function fromRequest(ScenarioRequest $request)
    {
        $request->loadMissing(['client']);

        return self::make([
            'request_id' => $request->id,
            'client_id' => $request->client->id,
            'status' => Status::PENDING,
            'price' => $request->settings->value,
            'failure_chance' => $request->settings->initialfailurechance,
            'location' => Location::collect()->random(),
        ]);
    }
}
