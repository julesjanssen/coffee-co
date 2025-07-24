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
}
