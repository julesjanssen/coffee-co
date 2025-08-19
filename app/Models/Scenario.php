<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Locale;
use App\Enums\Scenario\Status;
use App\Values\GameRound;
use App\Values\ScenarioSettings;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Scenario extends Model
{
    use HasFactory;
    use HasSqids;
    use UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'locale' => Locale::class,
        'settings' => ScenarioSettings::class,
        'status' => Status::class,
    ];

    protected $attributes = [
        'locale' => Locale::EN,
        'settings' => '[]',
        'status' => Status::DRAFT,
    ];

    /**
     * @return HasMany<ScenarioClient, $this>
     */
    public function clients(): HasMany
    {
        return $this->hasMany(ScenarioClient::class, 'scenario_id', 'id');
    }

    /**
     * @return HasMany<ScenarioRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(ScenarioRequest::class, 'scenario_id', 'id');
    }

    /**
     * @return HasMany<ScenarioProduct, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(ScenarioProduct::class, 'scenario_id', 'id');
    }

    /**
     * @return HasMany<ScenarioTip, $this>
     */
    public function tips(): HasMany
    {
        return $this->hasMany(ScenarioTip::class, 'scenario_id', 'id');
    }

    public static function selectByGroup()
    {
        /** @phpstan-ignore method.notFound */
        return Scenario::query()
            ->withExpression('scenario_groups', function ($query) {
                $query->from('scenarios', 's')
                    ->select([
                        's.*',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY group_id ORDER BY id DESC) as g'),
                    ]);
            })
            ->from('scenario_groups')
            ->where('g', '=', 1);
    }

    #[Scope]
    protected function whereActive(Builder $query)
    {
        $query->where('status', '=', Status::ACTIVE);
    }

    public function years(): int
    {
        return $this->settings->years;
    }

    public function numberOfRounds(): int
    {
        return $this->years() * GameRound::ROUNDS_PER_YEAR;
    }

    public function isDraft()
    {
        return $this->status->is(Status::DRAFT);
    }

    public function isArchived()
    {
        return $this->status->is(Status::ARCHIVED);
    }
}
