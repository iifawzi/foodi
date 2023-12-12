<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [

        ];

        if (DB::table('product')->count() !== 0) {
            DB::table('product')->delete();
        }
        DB::table('product')->insert($products);
    }
}
