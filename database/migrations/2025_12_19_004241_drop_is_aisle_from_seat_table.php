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
        Schema::table('seat', function (Blueprint $table) {
            $table->dropColumn('is_aisle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat', function (Blueprint $table) {
            $table->boolean('is_aisle')->default(false)->after('seat_type');
        });
    }
};
