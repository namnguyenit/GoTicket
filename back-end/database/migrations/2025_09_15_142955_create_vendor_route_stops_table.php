<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('vendor_route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_route_id')->constrained('vendor_routes')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->enum('stop_type', ['pickup', 'dropoff']);
            $table->integer('stop_order');
            $table->integer('offset_minutes_from_departure')->default(0);
            $table->unique(['vendor_route_id', 'stop_id', 'stop_type']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('vendor_route_stops');
    }
};
