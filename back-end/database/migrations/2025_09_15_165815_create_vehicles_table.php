<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); // BIGINT unsigned
            $table->foreignId('vendor_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('name', 100); // ví dụ: "Tàu SE1" hoặc tên xe bus
            $table->enum('vehicle_type', ['bus', 'train']);
            $table->string('license_plate', 50)->unique()->nullable(); // nullable cho tàu
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};