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
        Schema::table('order_line', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['product_id'])->references(['id'])->on('product')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['seat_id'])->references(['id'])->on('seat')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_line', function (Blueprint $table) {
            $table->dropForeign('order_line_order_id_foreign');
            $table->dropForeign('order_line_product_id_foreign');
            $table->dropForeign('order_line_seat_id_foreign');
        });
    }
};
