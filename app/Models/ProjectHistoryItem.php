<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Project\Status;
use App\Values\GameRound;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ProjectHistoryItem extends Model
{
    use UsesTenantConnection;

    protected $table = 'project_history';

    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
    ];

    protected $attributes = [];

    public $timestamps = false;

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /** @return Attribute<GameRound, never> */
    protected function round(): Attribute
    {
        return Attribute::make(
            get: fn() => new GameRound($this->project->session->scenario, $this->round_id)
        );
    }

    #[Scope]
    protected function selectAverageUptime(Builder $query)
    {
        $grammar = $query->getQuery()->getGrammar();

        $avgUptime = vsprintf('AVG(CASE WHEN %s = %s THEN 100 WHEN %s = %s THEN 0 END) as avg_uptime', [
            $grammar->wrap($this->getTable() . '.status'),
            $grammar->quoteString(Status::ACTIVE->value),
            $grammar->wrap($this->getTable() . '.status'),
            $grammar->quoteString(Status::DOWN->value),
        ]);

        $query->selectRaw($avgUptime);
    }
}
