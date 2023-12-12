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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->uuid('transaction_id')->primary();
            $table->unsignedInteger("quantity");
            $table->text("reason");
            $table->string("type");
            $table->timestamps();

            $table->foreignId("ingredient_id");
            $table->foreign("ingredient_id")->on("ingredient_stocks")->references("ingredient_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
