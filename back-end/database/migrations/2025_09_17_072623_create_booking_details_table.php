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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('seat_id');
            $table->decimal('price_at_booking', 10, 2);
            $table->primary(['booking_id','trip_id','seat_id']);
            // composite foreign key to trip_seats (trip_id, seat_id)
            $table->foreign(['trip_id','seat_id'])->references(['trip_id','seat_id'])->on('trip_seats')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
