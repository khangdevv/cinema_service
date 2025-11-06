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
        Schema::table('showtime', function (Blueprint $table) {
            $table->foreign(['movie_id'])->references(['id'])->on('movie')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['screen_id'])->references(['id'])->on('screen')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtime', function (Blueprint $table) {
            $table->dropForeign('showtime_movie_id_foreign');
            $table->dropForeign('showtime_screen_id_foreign');
        });
    }
};
