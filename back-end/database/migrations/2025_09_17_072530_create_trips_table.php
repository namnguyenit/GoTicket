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
    Schema::create('trips', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vendor_route_id')->constrained('vendor_routes')->onDelete('cascade');
        $table->dateTime('departure_datetime');
        $table->dateTime('arrival_datetime');
        $table->decimal('base_price', 10, 2);
        $table->enum('status', ['scheduled','ongoing','completed','cancelled'])->default('scheduled');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
