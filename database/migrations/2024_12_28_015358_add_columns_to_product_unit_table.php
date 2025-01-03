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
        Schema::table('product_unit', function (Blueprint $table) {
            $table->double('price')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('added_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_unit', function (Blueprint $table) {
            //
        });
    }
};
