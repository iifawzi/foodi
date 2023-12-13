<?php

namespace Database\Seeders;

use Database\Seeders\traits\SeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
{
    use SeederHelper;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->TruncateTable("merchants");
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

        DB::table("merchants")->insert($merchants);
    }
}
