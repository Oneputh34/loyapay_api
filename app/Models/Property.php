<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'address',
    ];

    /*
    |--------------------------------------------------------------------------
    | Owner
    |--------------------------------------------------------------------------
    */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Units
    |--------------------------------------------------------------------------
    */

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Leases
    |--------------------------------------------------------------------------
    */

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
}