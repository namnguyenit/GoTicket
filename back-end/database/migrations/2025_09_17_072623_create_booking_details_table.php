<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('booking_details', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('trip_id'); // Không cần constrained vì là khóa ngoại phức hợp
            $table->foreignId('seat_id'); // Không cần constrained vì là khóa ngoại phức hợp
            $table->foreignId("pickup_stop_id")->constrained('stops')->cascadeOnDelete();
            $table->foreignId("dropoff_stop_id")->constrained('stops')->cascadeOnDelete();
            $table->decimal('price_at_booking', 10, 2);
            $table->primary(['booking_id','trip_id','seat_id']);
            $table->foreign(['trip_id','seat_id'])->references(['trip_id','seat_id'])->on('trip_seats')->cascadeOnDelete();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
