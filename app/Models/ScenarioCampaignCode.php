<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Scenario\CampaignCodeCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ScenarioCampaignCode extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'scenario_campaigncodes';

    protected $guarded = [];

    protected $casts = [
        'category' => CampaignCodeCategory::class,
    ];

    protected $attributes = [
        'category' => CampaignCodeCategory::DIFFICULT,
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
