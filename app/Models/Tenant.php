<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Compte utilisateur du locataire
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Contrats de location
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    // Paiements effectués
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}