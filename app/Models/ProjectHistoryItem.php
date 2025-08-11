<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Project\Status;
use App\Values\GameRound;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ProjectHistoryItem extends Model
{
    use HasSqids;
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
}
