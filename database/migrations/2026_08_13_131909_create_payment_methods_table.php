<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            // Nom affiché à l'utilisateur
            $table->string('name');

            // Identifiant interne du moyen de paiement
            $table->string('code')->unique();

            // Prestataire de paiement
            $table->string('provider')->nullable();

            // Permet d'activer/désactiver un moyen de paiement
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};