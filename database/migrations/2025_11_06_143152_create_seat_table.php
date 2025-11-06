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
        Schema::create('seat', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('screen_id')->index();
            $table->string('row_label', 5);
            $table->integer('seat_number');
            $table->enum('seat_type', ['STANDARD', 'VIP', 'COUPLE', 'ACCESSIBLE'])->default('STANDARD');
            $table->boolean('is_aisle')->default(false);
            $table->boolean('is_blocked')->default(false);

            $table->unique(['screen_id', 'row_label', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat');
    }
};
