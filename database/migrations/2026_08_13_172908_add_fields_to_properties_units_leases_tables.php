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
        /*
        |--------------------------------------------------------------------------
        | Properties
        |--------------------------------------------------------------------------
        */

        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('owner_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name')->after('owner_id');

            $table->text('address')->after('name');
        });

        /*
        |--------------------------------------------------------------------------
        | Units
        |--------------------------------------------------------------------------
        */

        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('property_id')
                ->after('id')
                ->constrained('properties')
                ->cascadeOnDelete();

            $table->string('name')->after('property_id');

            $table->string('type')->nullable()->after('name');

            $table->decimal('rent_amount', 12, 2)
                ->after('type');

            $table->enum('status', [
                'vacant',
                'occupied',
                'maintenance'
            ])
                ->default('vacant')
                ->after('rent_amount');
        });

        /*
        |--------------------------------------------------------------------------
        | Leases
        |--------------------------------------------------------------------------
        */

        Schema::table('leases', function (Blueprint $table) {
            $table->foreignId('landlord_id')
                ->after('id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('tenant_id')
                ->after('landlord_id')
                ->constrained('tenants')
                ->restrictOnDelete();

            $table->foreignId('property_id')
                ->after('tenant_id')
                ->constrained('properties')
                ->restrictOnDelete();

            $table->foreignId('unit_id')
                ->after('property_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->decimal('rent_amount', 12, 2)
                ->after('unit_id');

            $table->unsignedTinyInteger('due_day')
                ->after('rent_amount');

            $table->date('start_date')
                ->after('due_day');

            $table->date('end_date')
                ->nullable()
                ->after('start_date');

            $table->enum('status', [
                'active',
                'ended',
                'terminated'
            ])
                ->default('active')
                ->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Leases
        |--------------------------------------------------------------------------
        */

        Schema::table('leases', function (Blueprint $table) {
            $table->dropForeign(['landlord_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['property_id']);
            $table->dropForeign(['unit_id']);

            $table->dropColumn([
                'landlord_id',
                'tenant_id',
                'property_id',
                'unit_id',
                'rent_amount',
                'due_day',
                'start_date',
                'end_date',
                'status',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Units
        |--------------------------------------------------------------------------
        */

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['property_id']);

            $table->dropColumn([
                'property_id',
                'name',
                'type',
                'rent_amount',
                'status',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Properties
        |--------------------------------------------------------------------------
        */

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);

            $table->dropColumn([
                'owner_id',
                'name',
                'address',
            ]);
        });
    }
};