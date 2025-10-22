<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete();
            $table->string('seat_number', 10);
            $table->unique(['coach_id','seat_number']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
