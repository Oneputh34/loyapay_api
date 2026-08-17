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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Échéance de loyer concernée
            $table->foreignId('rent_schedule_id')
                ->constrained('rent_schedules')
                ->restrictOnDelete();

            // Locataire qui effectue le paiement
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->restrictOnDelete();

            // Bailleur qui reçoit le paiement
            $table->foreignId('landlord_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Moyen de paiement utilisé
            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->restrictOnDelete();

            // Montant réellement payé
            $table->decimal('amount', 12, 2);

            // Identifiant de la transaction chez le prestataire
            $table->string('transaction_id')->nullable()->unique();

            // Référence interne de LoyaPay
            $table->string('reference')->unique();

            // État du paiement
            $table->enum('status', [
                'pending',
                'successful',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');

            // Informations supplémentaires du prestataire
            $table->text('provider_response')->nullable();

            // Date de confirmation du paiement
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};