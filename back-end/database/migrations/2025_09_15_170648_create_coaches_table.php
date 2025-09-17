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
            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // identifier: mã định danh toa hoặc chính xe bus (nếu bus chỉ có 1 coach)
            $table->string('identifier', 50);
            $table->enum('coach_type', ['sleeper_vip', 'sleeper_regular', 'seat_soft', 'seat_hard', 'limousine']);
            $table->unsignedSmallInteger('total_seats');
            $table->unique(['vehicle_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};