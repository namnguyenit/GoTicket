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
        Schema::create('vendor_route_stops', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vendor_route_id');
            $table->unsignedInteger('stop_id');
            $table->enum('stop_type', ['pickup', 'dropoff']);
            $table->integer('stop_order');
            $table->integer('offset_minutes_from_departure')->default(0);

            $table->foreign('vendor_route_id')->references('id')->on('vendor_routes')->cascadeOnDelete();
            $table->foreign('stop_id')->references('id')->on('stops')->cascadeOnDelete();
            $table->unique(['vendor_route_id', 'stop_id', 'stop_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_route_stops');
    }
};
