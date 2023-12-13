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
        Schema::create('low_stock_notifications', function (Blueprint $table) {
            $table->uuid("notification_id")->primary();
            $table->string("status");
            $table->timestamps();

            $table->foreignId("ingredient_id")->type("integer");
            $table->foreign("ingredient_id")->on("ingredient_stocks")->references("ingredient_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('low_stock_notifications');
    }
};
