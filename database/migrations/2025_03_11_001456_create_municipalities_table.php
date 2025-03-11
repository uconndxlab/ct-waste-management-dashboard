<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->year('year');
            $table->decimal('bulky_waste', 10, 2)->nullable();
            $table->decimal('recycling', 10, 2)->nullable();
            $table->decimal('tipping_fees', 10, 2)->nullable();
            $table->decimal('admin_costs', 10, 2)->nullable();
            $table->decimal('hazardous_waste', 10, 2)->nullable();
            $table->decimal('contractual_services', 10, 2)->nullable();
            $table->decimal('landfill_costs', 10, 2)->nullable();
            $table->decimal('total_sanitation_refuse', 10, 2)->nullable();
            $table->decimal('only_public_works', 10, 2)->nullable();
            $table->decimal('transfer_station_wages', 10, 2)->nullable();
            $table->decimal('hauling_fees', 10, 2)->nullable();
            $table->decimal('curbside_pickup_fees', 10, 2)->nullable();
            $table->decimal('waste_collection', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
