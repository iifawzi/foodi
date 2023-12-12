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
        Schema::create('ingredient_stock', function (Blueprint $table) {
            $table->unsignedBigInteger('ingredient_id')->unsigned()->autoIncrement();
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('available_quantity');
            $table->unsignedInteger('full_quantity');
            $table->unsignedInteger('min_threshold_percentage');
            $table->timestamps();

            $table->foreignId('merchant_id');
            $table->foreign("merchant_id")->on('merchant')->references("merchant_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_stock');
    }
};
