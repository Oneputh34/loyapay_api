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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            // Propriété à laquelle appartient le logement
            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();

            // Informations sur le logement
            $table->string('name');
            $table->string('type');
            $table->decimal('rent_amount', 12, 2);
            $table->enum('status', [
                'available',
                'occupied',
                'maintenance'
            ])->default('available');

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};