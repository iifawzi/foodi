<?php

namespace Database\Seeders;

use App\Models\Product;
use Database\Seeders\traits\SeederHelper;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use SeederHelper;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->TruncateTable("products");
        $productsData = [
            [
                "product_id" => 1,
                "name" => "Foodi Product 1",
                "description" => "Foodi 1 is one of the best products in the world",
                "price" => 100,
                "merchant_id" => 1
            ],
            [
                "product_id" => 2,
                "name" => "Foodi Product 2",
                "description" => "Foodi 2 is one of the second best products in the world",
                "price" => 200,
                "merchant_id" => 1
            ],
            [
                "product_id" => 3,
                "name" => "Foodi Product 3",
                "description" => "Foodi 3 is not the best than any other product, but I love it.",
                "price" => 400,
                "merchant_id" => 1
            ]
        ];

        foreach ($productsData as $productData) {
            $product = Product::create($productData);
            // Attach ingredients
            $product->ingredientStocks()->sync([
                1 => ['base_quantity' => 150],
                2 => ['base_quantity' => 30],
                3 => ['base_quantity' => 20],
            ]);
        }
    }
}
