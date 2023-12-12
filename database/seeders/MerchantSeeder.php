<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = [
            [
                'merchant_id' => 1,
                'email' => 'foodiPizza@example.com',
                'name' => 'Foodi Pizza',
            ],
            [
                'merchant_id' => 2,
                'email' => 'foodiBurger@example.com',
                'name' => 'Foodi Burger',
            ],
            [
                'merchant_id' => 3,
                'email' => 'foodiShawerma@example.com',
                'name' => 'Foodi Shawerma',
            ]
        ];

        if (DB::table('merchant')->count() !== 0) {
            DB::table('merchant')->delete();
        }
        DB::table('merchant')->insert($merchants);
    }
}
