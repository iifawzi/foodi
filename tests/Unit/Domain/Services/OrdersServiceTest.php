<?php

namespace Tests\Unit\Domain\Services;

use Src\Domain\Entities\Ingredient;
use Src\Domain\Entities\Item;
use Src\Domain\Entities\Merchant;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\StockItem;
use Src\Domain\Services\OrderUseCases;
use Src\Domain\Types\OrderStatus;
use Tests\TestCase;

class OrdersServiceTest extends TestCase
{
    public function test_order_confirmed_if_enough_stock(): void
    {
        $merchant = new Merchant(1, 'Fawzi', 'iifawzie@gmail.com');
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

        $stock1 = new StockItem(1, 'beef', 50, 50, 50);
        $stock2 = new StockItem(2, 'beef', 100, 90, 50);
        $stock3 = new StockItem(3, 'beef', 60, 100, 50);

        $orderService = new OrderUseCases();
        $stockItems = [$stock1, $stock2, $stock3];
        $orderStatus = $orderService->confirmOrder($merchant, $order, $stockItems);

        $this->assertEquals(true, $orderStatus);
        $this->assertEquals(OrderStatus::CONFIRMED, $order->getStatus());
    }

    public function test_order_cancelled_if_insufficient_stock(): void
    {
        $merchant = new Merchant(1, 'Fawzi', 'iifawzie@gmail.com');
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

        $stock1 = new StockItem(1, 'beef', 50, 50, 50);
        $stock2 = new StockItem(2, 'Cheese', 100, 90, 50);
        $stock3 = new StockItem(3, 'Onion', 60, 99, 50);

        $orderService = new OrderUseCases();
        $stockItems = [$stock1, $stock2, $stock3];
        $orderStatus = $orderService->confirmOrder($merchant, $order, $stockItems);

        $this->assertEquals(false, $orderStatus);
        $this->assertEquals(OrderStatus::CANCELLED, $order->getStatus());
    }

    public function test_merchant_notified_about_low_stock_only_once(): void
    {
        $merchant = new Merchant(1, 'Fawzi', 'iifawzie@gmail.com');
        $ingredient1 = new Ingredient(1, 26);
        $ingredient2 = new Ingredient(2, 10);

        $item = new Item(1, 'Foodi Burger', 100, 2);
        $item->setIngredient($ingredient1);

        $item2 = new Item(2, 'idoof Burger', 100, 1);
        $item2->setIngredient($ingredient2);

        $order = new Order();
        $order->addItems([$item]);
        $order->addItems([$item2]);

        $stock1 = new StockItem(1, 'Beef', 100, 100, 50);
        $stock2 = new StockItem(2, 'Cheese', 100, 90, 50);

        $orderService = new OrderUseCases();
        $stockItems = [$stock1, $stock2];

        $orderService->confirmOrder($merchant, $order, $stockItems);
        // first time the stock go below threshold, expecting merchant to have been notified.
        $this->assertEquals([$stock1], $merchant->getItemsToRefill());

        // second order with item using the same ingredient.
        $item3 = new Item(3, 'Blue cheese foodi', 100, 1);
        $item3->setIngredient($ingredient1);
        $order2 = new Order();
        $order2->addItems([$item]);
        $orderService->confirmOrder($merchant, $order2, $stockItems);

        // second time the stock go below threshold, merchant shouldn't be notified again.
        // expecting the array to contain the notification only once.
        $this->assertEquals([$stock1], $merchant->getItemsToRefill());
    }

}
