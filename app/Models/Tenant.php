<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\Uri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as Base;

class Tenant extends Base
{
    use HasFactory;
    use HasSqids;
    use UsesLandlordConnection;

    protected $guarded = [];

    protected $attributes = [
        'settings' => '[]',
    ];

    /**
     * @return array{
     *  settings: 'array'
     * }
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function getHost()
    {
        if (str_contains($this->slug, '.')) {
            return $this->slug;
        }

        $base = new Uri(config('app.url'));

        return $this->slug . '.' . $base->getHost();
    }

    public function getDatabaseName(): string
    {
        return vsprintf('%s-%s', [
            config('database.connections.landlord.database'),
            $this->slug,
        ]);
    }
}
