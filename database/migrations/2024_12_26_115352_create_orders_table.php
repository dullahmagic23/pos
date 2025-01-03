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
            $table->id();
            $table->unsignedBigInteger('product_id')->references('id')->on('products');
            $table->unsignedBigInteger('unit_id')->references('id')->on('units');
            $table->unsignedBigInteger('supplier_id')->references('id')->on('suppliers');
            $table->double('buying_price');
            $table->integer('quantity');
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->timestamps();
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
