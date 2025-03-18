<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('municipality_financial_data', function (Blueprint $table) {
            $table->id();
            $table->string('municipality');
            $table->string('time_period')->nullable();
            $table->string('link')->nullable();
            $table->integer('population')->nullable();
            $table->float('size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('municipality_financial_data');
    }
    
};
