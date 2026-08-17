<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant
    |--------------------------------------------------------------------------
    */

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Leases as landlord
    |--------------------------------------------------------------------------
    */

    public function leasesAsLandlord(): HasMany
    {
        return $this->hasMany(Lease::class, 'landlord_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }
}