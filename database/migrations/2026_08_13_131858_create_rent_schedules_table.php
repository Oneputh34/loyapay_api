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
        Schema::create('rent_schedules', function (Blueprint $table) {
            $table->id();

            // Contrat concerné
            $table->foreignId('lease_id')
                ->constrained('leases')
                ->cascadeOnDelete();

            // Période du loyer
            $table->date('period');

            // Date à laquelle le loyer doit être payé
            $table->date('due_date');

            // Montant à payer
            $table->decimal('amount', 12, 2);

            // État du loyer
            $table->enum('status', [
                'pending',
                'paid',
                'overdue',
                'cancelled'
            ])->default('pending');

            // Date réelle du paiement
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // Un seul loyer par contrat et par période
            $table->unique(['lease_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_schedules');
    }
};