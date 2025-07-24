<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Product\Color;
use App\Enums\Product\Material;
use App\Enums\Product\Type;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ScenarioProduct extends Model
{
    use HasSqids;
    use UsesTenantConnection;

    protected $table = 'scenario_products';

    protected $guarded = [];

    protected $casts = [
        'type' => Type::class,
        'material' => Material::class,
        'color' => Color::class,
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

    /** @return Attribute<string, never> */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->type->description();
                $specs = collect([
                    $this->material,
                    $this->color,
                ])->filter()->map(fn($v) => $v->description());

                if ($specs->isNotEmpty()) {
                    $value .= ' (' . $specs->join(', ') . ')';
                }

                return $value;
            }
        )->shouldCache();
    }
}
