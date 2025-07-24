<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Project\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Project extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'price' => 'int',
        'failure_chance' => 'int',
        'downtime' => 'int',
        'location' => 'int',
    ];

    protected $attributes = [];

    public $timestamps = false;

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
}
