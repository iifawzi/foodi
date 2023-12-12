<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\traits\SeederHelper;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use SeederHelper;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->disableForeignKeysChecks();
        $this->call([MerchantSeeder::class, IngredientStockSeeder::class, ProductSeeder::class]);
        $this->enableForeignKeysChecks();

    }
}
