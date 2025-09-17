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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();// miss nullable()
            $table->string('transaction_id', 255)->nullable()->unique();
            $table->decimal('amount', 10, 2);// miss nullable()
            $table->string('payment_method', 50);
            $table->enum('status', ['success','failed','pending'])->default('pending');
            $table->timestamp('paid_at');
            $table->unique('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
