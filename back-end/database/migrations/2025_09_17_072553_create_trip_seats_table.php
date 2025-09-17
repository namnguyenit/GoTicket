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
        Schema::create('trip_seats', function (Blueprint $table) {
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete(); // miss nullable()
            $table->foreignId('seat_id')->constrained('seats')->cascadeOnDelete(); // miss nullable()
            $table->decimal('price', 10, 2); // miss nullable()
            $table->enum('status', ['available','booked','locked','disabled'])->default('available');
            $table->primary(['trip_id','seat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_seats');
    }
};
