<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stops', function (Blueprint $table) {
            if (!Schema::hasColumn('stops', 'transport_type')) {
                $table->enum('transport_type', ['bus','train'])->default('bus')->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stops', function (Blueprint $table) {
            if (Schema::hasColumn('stops', 'transport_type')) {
                $table->dropColumn('transport_type');
            }
        });
    }
};
