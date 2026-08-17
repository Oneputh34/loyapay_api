<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'period',
        'due_date',
        'amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'period' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Contrat
    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    // Paiements associés à cette échéance
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}