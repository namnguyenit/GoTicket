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
        Schema::create('trip_coaches', function (Blueprint $table) {
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();// miss nullable()
            $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete(); // miss nullable()
            $table->unsignedSmallInteger('coach_order')->default(1);
            $table->primary(['trip_id','coach_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_coaches');
    }
};
