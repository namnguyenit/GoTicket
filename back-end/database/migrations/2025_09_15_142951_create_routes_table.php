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
        Schema::create('routes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('origin_location_id');
            $table->unsignedInteger('destination_location_id');

            
            $table->foreign('origin_location_id')->references('id')->on('locations')->cascadeOnDelete();
            $table->foreign('destination_location_id')->references('id')->on('locations')->cascadeOnDelete();

            // Đảm bảo không có tuyến đường trùng lặp
            $table->unique(['origin_location_id', 'destination_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
