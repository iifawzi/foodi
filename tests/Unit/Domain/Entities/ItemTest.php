<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use Src\Domain\Entities\Ingredient;
use Src\Domain\Entities\Item;

class ItemTest extends TestCase
{
    public function test_item_initialized_correctly(): void
    {
        $item = new Item(1, 'Foodi Burger', 100, 2);
        $this->assertEquals(200, $item->getTotalPrice(200));
        $this->assertEquals(1, $item->getId());
        $this->assertEquals(2, $item->getQuantity());
        $this->assertEquals('Foodi Burger', $item->getName());
        $this->assertEquals(100, $item->getUnitPrice());
    }
    public function test_total_price_correctly_calculated(): void
    {
        $item = new Item(1, 'Foodi Burger', 100, 2);
        $this->assertEquals(200, $item->getTotalPrice(200));
    }

    public function test_ingredients_added_correctly(): void
    {
        $ingredient1 = new Ingredient(1, 10);
        $ingredient2 = new Ingredient(2, 10);

        $ingredients = [1 => $ingredient1, 2 => $ingredient2];

        $item = new Item(1, 'Foodi Burger', 100, 2);

        $item->setIngredient($ingredient1);
        $item->setIngredient($ingredient2);
        $this->assertEquals($ingredients, $item->getIngredients());

    }
}
