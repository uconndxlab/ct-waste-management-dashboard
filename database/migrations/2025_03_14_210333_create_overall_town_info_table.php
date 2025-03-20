<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('overall_town_info', function (Blueprint $table) {
            $table->id();
            $table->string('municipality');
            $table->string('department')->nullable();
            $table->string('contact_1')->nullable();
            $table->string('title_1')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('email_1')->nullable();
            $table->string('contact_2')->nullable();
            $table->string('title_2')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('email_2')->nullable();
            $table->text('notes')->nullable();
            $table->text('other_useful_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('overall_town_info');
    }
};
