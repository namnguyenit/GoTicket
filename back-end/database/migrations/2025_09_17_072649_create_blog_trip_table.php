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
        Schema::create('blog_trip', function (Blueprint $table) {
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();// miss nullable()
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();// miss nullable()
            $table->primary(['blog_id','trip_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_trip');
    }
};
