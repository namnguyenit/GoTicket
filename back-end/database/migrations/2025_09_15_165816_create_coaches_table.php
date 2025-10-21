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

            // Tên định danh cho toa, ví dụ: "Toa 1", "Toa 2", "VIP-A"
            $table->string('identifier', 50);

            // Loại toa/xe
            $table->enum('coach_type', [
                'sleeper_vip',      
                'sleeper_regular',  
                'limousine',        
                'seat_soft',        
                'seat_VIP'
            ]);

            // Tổng số ghế trong toa/xe này
            $table->integer('total_seats');

            // Đảm bảo mỗi toa trong một đoàn tàu/xe có tên định danh duy nhất
            $table->unique(['vehicle_id', 'identifier']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};