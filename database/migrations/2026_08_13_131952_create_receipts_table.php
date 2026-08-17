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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            // Paiement concerné
            $table->foreignId('payment_id')
                ->unique()
                ->constrained('payments')
                ->restrictOnDelete();

            // Numéro unique de la quittance
            $table->string('receipt_number')->unique();

            // Montant payé
            $table->decimal('amount', 12, 2);

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
        Schema::dropIfExists('receipts');
    }
};