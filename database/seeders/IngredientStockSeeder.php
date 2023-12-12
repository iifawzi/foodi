<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredientsStock = [
            [
                'ingredient_id' => 1,
                'name' => 'Beef',
                'description' => 'Juicy Egyptian Beef',
                'full_quantity' => 1000,
                'available_quantity' => 1000,
                'min_threshold_percentage' => 50,
                'merchant_id' => 1,
            ],
            [
                'ingredient_id' => 2,
                'name' => 'Onion',
                'description' => 'What\'s better than Egyptian Onion?',
                'full_quantity' => 500,
                'available_quantity' => 500,
                'min_threshold_percentage' => 50,
                'merchant_id' => 1,
            ],
            [
                'ingredient_id' => 2,
                'name' => 'Cheese',
                'description' => 'What\'s better than Swiss Cheese?',
                'full_quantity' => 2000,
                'available_quantity' => 2000,
                'min_threshold_percentage' => 50,
                'merchant_id' => 1,
            ],
        ];

        if (DB::table('ingredient_Stock')->count() !== 0) {
            DB::table('ingredient_Stock')->delete();
        }
        DB::table('ingredient_Stock')->insert($ingredientsStock);
    }
}
