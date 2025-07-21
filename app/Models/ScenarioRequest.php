<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class ScenarioRequest extends Model
{
    use HasSqids;
    use UsesLandlordConnection;

    protected $table = 'scenario_requests';

    protected $guarded = [];

    protected $casts = [
        'requirements' => 'array',
        'settings' => 'array',
    ];

    protected $attributes = [
        'requirements' => '[]',
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
