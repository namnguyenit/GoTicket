<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('vendor_id')
                  ->constrained()
                  ->cascadeOnDelete(); //don't see relationship

            $table->string('name', 100); 
            $table->enum('vehicle_type', ['bus', 'train']); // miss nullable
            $table->string('license_plate', 50)->unique()->nullable(); 
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};