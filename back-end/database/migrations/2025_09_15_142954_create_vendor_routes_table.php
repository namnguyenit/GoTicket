<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
    Schema::create('vendor_routes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
        $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
        $table->string('name');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('vendor_routes');
    }
};
