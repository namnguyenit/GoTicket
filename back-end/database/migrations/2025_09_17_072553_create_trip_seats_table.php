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
            $table->unsignedInteger('trip_id');
            $table->unsignedInteger('seat_id');
            $table->decimal('price', 10, 2); 
            $table->enum('status', ['available','booked','locked','disabled'])->default('available');
            $table->primary(['trip_id','seat_id']);
            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
            $table->foreign('seat_id')->references('id')->on('seats')->cascadeOnDelete();
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
