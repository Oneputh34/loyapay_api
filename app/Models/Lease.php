<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'tenant_id',
        'property_id',
        'unit_id',
        'rent_amount',
        'due_day',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rent_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Bailleur
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    // Locataire
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Propriété
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Logement
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Échéances de loyer
    public function rentSchedules()
    {
        return $this->hasMany(RentSchedule::class);
    }
}