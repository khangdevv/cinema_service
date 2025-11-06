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
        Schema::create('screen', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('format', 20)->nullable()->default('2D');
            $table->integer('row_count');
            $table->integer('col_count');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen');
    }
};
