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
        Schema::create('order_line', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('order_id');
            $table->enum('item_type', ['TICKET', 'PRODUCT']);
            $table->bigInteger('seat_id')->nullable()->index();
            $table->bigInteger('product_id')->nullable()->index();
            $table->integer('qty')->default(1);
            $table->integer('unit_price');
            $table->integer('line_total');

            $table->index(['order_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_line');
    }
};
