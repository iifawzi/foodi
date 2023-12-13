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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger("quantity");
            $table->unsignedInteger('total_price');

            $table->string('order_id');
            $table->foreign('order_id')->on('orders')->references('order_id');

            $table->unsignedInteger('product_id')->type("integer");
            $table->foreign('product_id')->on('products')->references('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
