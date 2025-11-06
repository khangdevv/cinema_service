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
        Schema::create('account', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('email')->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password_hash')->nullable();
            $table->string('full_name')->nullable();
            $table->enum('role', ['CUSTOMER', 'STAFF', 'ADMIN'])->default('CUSTOMER');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account');
    }
};
