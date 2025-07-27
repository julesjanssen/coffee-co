<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ScenarioRequest extends Model
{
    use HasSqids;
    use UsesTenantConnection;

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

    /**
     * @return HasOne<ScenarioClient, $this>
     */
    public function client(): HasOne
    {
        return $this->hasOne(ScenarioClient::class, 'client_id', 'id');
    }

    /** @return Attribute<string, never> */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn() => 'test title',
        );
    }
}
