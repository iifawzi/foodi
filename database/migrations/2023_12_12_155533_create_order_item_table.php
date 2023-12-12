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
        Schema::create('order_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger("quantity");
            $table->unsignedInteger('total_price');
            $table->timestamps();

            $table->foreignUuid('order_id');
            $table->foreign('order_id')->on('order')->references('order_id');

            $table->foreignId('product_id');
            $table->foreign('product_id')->on('product')->references('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
};
