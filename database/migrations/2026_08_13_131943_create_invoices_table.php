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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Paiement concerné
            $table->foreignId('payment_id')
                ->unique()
                ->constrained('payments')
                ->restrictOnDelete();

            // Numéro de facture
            $table->string('invoice_number')->unique();

            // Montant facturé
            $table->decimal('amount', 12, 2);

            // État de la facture
            $table->enum('status', [
                'issued',
                'paid',
                'cancelled'
            ])->default('issued');

            // Chemin du fichier PDF
            $table->string('pdf_path')->nullable();

            // Date d'émission
            $table->timestamp('issued_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};