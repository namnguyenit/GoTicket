<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('identifier', 50);
            $table->enum('coach_type', ['sleeper_vip', 'sleeper_regular', 'seat_soft', 'seat_hard', 'limousine']);
            $table->integer('total_seats');
            $table->unique(['vehicle_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
