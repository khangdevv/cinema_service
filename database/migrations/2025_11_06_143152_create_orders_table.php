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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->enum('channel', ['WEB', 'POS'])->index();
            $table->bigInteger('account_id')->nullable()->index();
            $table->bigInteger('cashier_id')->nullable()->index();
            $table->bigInteger('showtime_id');
            $table->enum('status', ['INIT', 'PAID', 'CANCELLED'])->default('INIT');
            $table->enum('payment_method', ['CASH', 'CARD', 'EWALLET'])->nullable();
            $table->integer('total_amount')->default(0);

            $table->index(['showtime_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
