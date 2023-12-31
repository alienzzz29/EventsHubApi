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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->dateTime('date_sched_start');
            $table->dateTime('date_sched_end');
            $table->dateTime('date_reg_deadline');
            $table->integer('est_attendants');
            $table->string('location');
            $table->bigInteger('category_id');
            $table->bigInteger('venue_id');
            $table->integer('event_status');//0,1,2 ;0 = disabled, 1 = enabled, 2 = expired
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
