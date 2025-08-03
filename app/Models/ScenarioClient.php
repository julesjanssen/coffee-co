<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Client\Segment;
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

    public function netPromotorScoreForGameSession(GameSession $session)
    {
        // TODO: implement calc
        return 60;
    }

    public function listAvailableRequestForGameSession(GameSession $session, bool $includeLabConsoluting = false)
    {
        $mmmaActive = $session->isMMMAActive();
        $nps = $session->netPromotorScore();
        $marketingTresholdScore = $session->marketingTresholdScore();

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
            ->when(! $includeLabConsoluting, function ($q) {
                $q->where('requirements->labconsulting', '=', false);
            })
            ->where('requirements->tresholdNps', '<=', $nps)
            ->where('requirements->tresholdMarketing', '<=', $marketingTresholdScore)
            ->get()
            ->sortByDesc(fn($v) => (int) $v->settings['value'])
            ->values();
    }
}
