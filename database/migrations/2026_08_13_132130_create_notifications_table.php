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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Utilisateur qui reçoit la notification
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Type de notification
            $table->string('type');

            // Titre
            $table->string('title');

            // Message
            $table->text('message');

            // Permet de savoir si la notification a été lue
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};