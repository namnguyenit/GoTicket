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
        Schema::create('trip_coaches', function (Blueprint $table) {
            $table->unsignedInteger('trip_id');
            $table->unsignedInteger('coach_id');
            $table->integer('coach_order')->default(1);
            $table->primary(['trip_id','coach_id']);
            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
            $table->foreign('coach_id')->references('id')->on('coaches')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_coaches');
    }
};
