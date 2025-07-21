<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class ScenarioRequestSolution extends Model
{
    use HasSqids;
    use UsesLandlordConnection;

    protected $table = 'scenario_request_solutions';

    protected $guarded = [];

    protected $casts = [
        'product_ids' => AsCollection::class,
        'score' => 'integer',
        'is_optimal' => 'boolean',
    ];

    protected $attributes = [];

    public $timestamps = false;

    /**
     * @return BelongsTo<Scenario, $this>
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id', 'id');
    }

    /**
     * @return BelongsTo<ScenarioRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(ScenarioRequest::class, 'request_id', 'id');
    }
}
