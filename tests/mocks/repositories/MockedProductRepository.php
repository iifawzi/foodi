<?php

namespace Tests\mocks\repositories;

use Illuminate\Support\Collection;
use Src\Application\ports\infrastructure\repositories\ProductRepository;
use Src\Domain\Entities\Ingredient;
use Src\Domain\Entities\Item;

class MockedProductRepository implements ProductRepository
{
    private array $data;

    public function __construct()
    {
        $this->data = [
            "1" => ["id" => 1, "name" => "Burger 1", "price" => 100, "ingredients" => [
                "1" => ["id" => 1, "name" => "Beef", "quantity" => 150],
                "2" => ["id" => 2, "name" => "Cheese", "quantity" => 30],
                "3" => ["id" => 3, "name" => "Onion", "quantity" => 20],
            ]],
            "2" => ["id" => 1, "name" => "Burger Double", "price" => 200, "ingredients" => [
                "1" => ["id" => 1, "name" => "Beef", "quantity" => 300],
                "2" => ["id" => 2, "name" => "Cheese", "quantity" => 60],
                "3" => ["id" => 3, "name" => "Onion", "quantity" => 30],
            ]],
        ];
    }

    public function getItems(Collection $productQuantities): array
    {
        $items = [];
        $productQuantities->each(function ($quantity, $key) use(&$items) {
            $productData = $this->data[$key];
            $item = new Item($productData["id"], $productData["name"], $productData["price"], $quantity);;
            foreach ($productData["ingredients"] as $ingredient) {
                $item->setIngredient(new Ingredient($ingredient["id"], $ingredient["quantity"]));
            }
            $items[] = $item;
        });
        return $items;
    }
}
