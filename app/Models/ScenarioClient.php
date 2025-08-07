<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Client\CarBrand;
use App\Enums\Client\Market;
use App\Enums\Client\Segment;
use App\Enums\Client\YearsInBusiness;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ScenarioClient extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'scenario_clients';

    protected $guarded = [];

    protected $casts = [
        'player_id' => 'int',
        'segment' => Segment::class,
        'settings' => 'array',
    ];

    protected $attributes = [
        'settings' => '[]',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<Scenario, $this>
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id', 'id');
    }

    /** @return Attribute<Market, never> */
    protected function market(): Attribute
    {
        return Attribute::make(
            get: fn() => Market::from($this->settings['market']),
        );
    }

    /** @return Attribute<CarBrand, never> */
    protected function carBrand(): Attribute
    {
        return Attribute::make(
            get: fn() => CarBrand::from($this->settings['carbrand']),
        );
    }

    /** @return Attribute<YearsInBusiness, never> */
    protected function yearsInBusiness(): Attribute
    {
        return Attribute::make(
            get: fn() => YearsInBusiness::from($this->settings['years']),
        );
    }

    public function netPromotorScoreForGameSession(GameSession $session)
    {
        // TODO: implement calc
        return 60;
    }

    public function hasMaxProjectsForCurrentYear(GameSession $session)
    {
        $maxProjectsPerClientPerYear = $session->settings->maxProjectsPerClientPerYear;

        return $this->countProjectsForCurrentYear($session) >= $maxProjectsPerClientPerYear;
    }

    public function countProjectsForCurrentYear(GameSession $session)
    {
        return $session->projects()
            ->whereRequestedInYear($session->currentYear)
            ->whereClient($this)
            ->count();
    }

    /**
     * @return Collection<array-key, ScenarioRequest>
     */
    public function listAvailableRequestForGameSession(GameSession $session, bool $includeLabConsulting = false)
    {
        if ($this->hasMaxProjectsForCurrentYear($session)) {
            return collect();
        }

        $mmmaActive = $session->isMMMAActive();
        $nps = $session->netPromotorScore();
        $marketingTresholdScore = $session->marketingTresholdScore();

        if ($nps < 70) {
            $includeLabConsulting = false;
        }

        $projects = $session->projects()
            ->whereColumn('projects.request_id', 'scenario_requests.id')
            ->getQuery();

        return $session->scenario->requests()
            ->whereNotExists($projects)
            ->where('client_id', '=', $this->id)
            ->where('delay', '<=', $session->current_round_id)
            ->when(! $mmmaActive, function ($q) {
                $q->where('requirements->mmma', '=', false);
            })
            ->when(! $includeLabConsulting, function ($q) {
                $q->where('requirements->labconsulting', '=', false);
            })
            ->where('requirements->tresholdNps', '<=', $nps)
            ->where('requirements->tresholdMarketing', '<=', $marketingTresholdScore)
            ->get()
            ->sortByDesc(fn($v) => (int) $v->settings->value)
            ->values();
    }
}
