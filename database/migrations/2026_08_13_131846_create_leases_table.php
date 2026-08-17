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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();

            // Bailleur
            $table->foreignId('landlord_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Locataire
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // Propriété
            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();

            // Logement loué
            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete();

            // Informations financières du contrat
            $table->decimal('rent_amount', 12, 2);

            // Jour du mois où le loyer arrive à échéance
            $table->unsignedTinyInteger('due_day');

            // Durée du contrat
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // État du contrat
            $table->enum('status', [
                'active',
                'terminated',
                'expired'
            ])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};