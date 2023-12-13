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
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->unsignedInteger('base_quantity');

            $table->unsignedInteger('product_id');
            $table->unsignedInteger('ingredient_id');
            $table->foreign('product_id')->on('products')->references('product_id');
            $table->foreign('ingredient_id')->on('ingredient_stocks')->references('ingredient_id');

            $table->primary(['product_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
