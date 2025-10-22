<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('stops');
    }
};
