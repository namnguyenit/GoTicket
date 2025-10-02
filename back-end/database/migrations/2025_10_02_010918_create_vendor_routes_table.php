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
    Schema::create('vendor_routes', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('vendor_id');
        $table->unsignedInteger('route_id');
        $table->string('name');
        $table->boolean('is_active')->default(true);
        $table->timestamps(); // Thêm created_at và updated_at

        $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
        $table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_routes');
    }
};
