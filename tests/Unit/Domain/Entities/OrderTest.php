<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use Src\Domain\Entities\Ingredient;
use Src\Domain\Entities\Item;
use Src\Domain\Entities\Order;
use Src\Domain\Types\OrderStatus;

class OrderTest extends TestCase
{
    public function test_order_initialized_with_id_and_pending_status(): void
    {
        $order = new Order();
        $this->assertEquals(OrderStatus::PENDING, $order->getStatus());
    }

    public function test_item_added_correctly_to_list_of_items(): void
    {
        $ingredient1 = new Ingredient(1, 2);
        $item = new Item(1, 'Foodi Burger', 100, 2);
        $item->setIngredient($ingredient1);

        $order = new Order();
        $order->addItems([$item]);

        $expectedItems = [$item];
        $this->assertEquals($expectedItems, $order->getItems());
    }

    public function test_order_sum_needed_quantities_of_all_ingredients(): void
    {
        $ingredient1 = new Ingredient(1, 2);
        $ingredient2 = new Ingredient(2, 10);
        $ingredient3 = new Ingredient(3, 50);

        $item = new Item(1, 'Foodi Burger', 100, 2);
        $item->setIngredient($ingredient1);
        $item->setIngredient($ingredient3);


        $item2 = new Item(2, 'idoof Burger', 100, 1);
        $item2->setIngredient($ingredient1);
        $item2->setIngredient($ingredient2);


        $order = new Order();
        $order->addItems([$item]);
        $order->addItems([$item2]);

        // ingredient one's quantity is 2, used for 3 items (2 of item 1 & 1 of item 2) => 6
        // ingredient two's quantity is 10, used for 1 item (item 2) => 10
        // ingredient three's quantity is 50, used for 2 items (item 2 & item 1) => 100
        $expectedIngredients = [1 => 6, 2 => 10, 3 => 100];
        $this->assertEquals($expectedIngredients, $order->getIngredients());
    }
}
