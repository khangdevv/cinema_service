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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign(['account_id'])->references(['id'])->on('account')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['showtime_id'])->references(['id'])->on('showtime')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_account_id_foreign');
            $table->dropForeign('orders_showtime_id_foreign');
        });
    }
};
