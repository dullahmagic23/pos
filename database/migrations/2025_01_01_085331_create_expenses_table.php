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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('expense_category_id');
            $table->foreign('expense_category_id')->references('id')
                ->on('expense_categories')->cascadeOnDelete();
            $table->index('expense_category_id');
            $table->unsignedInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')
                ->on('payment_methods')->cascadeOnDelete();
            $table->index('payment_method_id');
            $table->tinyText('description');
            $table->double('amount');
            $table->string('supplier');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
