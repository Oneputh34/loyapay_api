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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Bailleur propriétaire de la propriété
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Informations sur la propriété
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->enum('type', [
                'house',
                'building',
                'villa',
                'commercial'
            ]);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};