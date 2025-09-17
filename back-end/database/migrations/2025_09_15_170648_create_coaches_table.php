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
                  ->cascadeOnDelete(); // don't see relationship

            
            $table->string('identifier', 50); // miss nullable()
            $table->enum('coach_type', ['sleeper_vip', 'sleeper_regular', 'seat_soft', 'seat_hard', 'limousine']); // miss nullable()
            $table->unsignedSmallInteger('total_seats'); //miss nullable()
            $table->unique(['vehicle_id', 'identifier']); // ??? what unique ??? 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};