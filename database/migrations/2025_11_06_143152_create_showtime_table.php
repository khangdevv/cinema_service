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
        Schema::create('showtime', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('movie_id');
            $table->bigInteger('screen_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('base_price');
            $table->enum('status', ['SCHEDULED', 'OPEN', 'CLOSED', 'CANCELLED'])->default('OPEN');

            $table->index(['movie_id', 'start_at']);
            $table->unique(['screen_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtime');
    }
};
