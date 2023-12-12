<?php

namespace Src\Infrastructure\repositories\Eloquent;

use App\Models\Product;
use Illuminate\Support\Collection;
use Src\Application\ports\infrastructure\repositories\ProductRepository;
use Src\Domain\Entities\Ingredient;
use Src\Domain\Entities\Item;

class EloquentProductRepository implements ProductRepository
{
    public function getItems(Collection $productQuantities): array
    {
        $productIds = $productQuantities->keys()->toArray();
        $products = Product::with("ingredientStocks")->whereIn("product_id", $productIds)->get()->all();

        $items = [];
        foreach ($products as $product) {
            $productId = $product->product_id;
            $quantity = $productQuantities[$productId];

            $item = new Item($productId, $product->name, $product->price, $quantity);
            foreach ($product->ingredientStocks as $ingredient) {
                $item->setIngredient(new Ingredient($ingredient->ingredient_id, $ingredient->pivot->base_quantity));
            }
            $items[] = $item;
        }

        return $items;
    }
}
