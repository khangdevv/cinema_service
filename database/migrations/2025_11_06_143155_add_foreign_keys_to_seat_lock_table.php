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
        Schema::table('seat_lock', function (Blueprint $table) {
            $table->foreign(['seat_id'])->references(['id'])->on('seat')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['showtime_id'])->references(['id'])->on('showtime')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['account_id'])->references(['id'])->on('account')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_lock', function (Blueprint $table) {
            $table->dropForeign('seat_lock_seat_id_foreign');
            $table->dropForeign('seat_lock_showtime_id_foreign');
            $table->dropForeign('seat_lock_account_id_foreign');
        });
    }
};
