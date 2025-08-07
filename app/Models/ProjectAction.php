<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Project\ActionType;
use App\Values\GameRound;
use App\Values\ProjectActionDetails;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ProjectAction extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'project_actions';

    protected $guarded = [];

    protected $casts = [
        'type' => ActionType::class,
        'details' => ProjectActionDetails::class,
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
