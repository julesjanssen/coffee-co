<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use RedExplosion\Sqids\Concerns\HasSqids;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasPasskeys
{
    use HasFactory;
    use HasRoles;
    use HasSqids;
    use InteractsWithPasskeys;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use UsesTenantConnection;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $attributes = [
        'password' => '',
    ];

    /**
     * @return array{
     *  email_verified_at: 'datetime',
     *  password: 'hashed',
     * }
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getMaxRoleLevelAttribute(): int
    {
        return (int) $this->roles()->max('level');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getAvatarAttribute()
    {
        return [
            'url' => 'https://gardiaan.jules.nl/avatar?' . http_build_query([
                'email' => $this->email,
                'name' => $this->name,
                'client' => config('app.name'),
            ]),
        ];
    }
}
