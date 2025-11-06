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
        Schema::create('seat_lock', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('showtime_id');
            $table->bigInteger('seat_id')->index();
            $table->bigInteger('account_id')->nullable()->index();
            $table->dateTime('expires_at')->index();

            $table->unique(['showtime_id', 'seat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_lock');
    }
};
