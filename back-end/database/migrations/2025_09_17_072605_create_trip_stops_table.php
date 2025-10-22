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
        Schema::create('trip_stops', function (Blueprint $table) {
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('stop_id')->constrained('stops')->cascadeOnDelete();
            $table->enum('stop_type', ['pickup','dropoff']);
            $table->time('scheduled_time');
            $table->primary(['trip_id', 'stop_id', 'stop_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_stops');
    }
};
