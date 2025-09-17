<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicle_id');
            $table->string('identifier', 50);
            $table->enum('coach_type', ['sleeper_vip', 'sleeper_regular', 'seat_soft', 'seat_hard', 'limousine']);
            $table->integer('total_seats');
            $table->unique(['vehicle_id', 'identifier']); 
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};