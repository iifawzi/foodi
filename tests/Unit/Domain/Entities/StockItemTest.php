<?php

namespace Unit\Domain;

use PHPUnit\Framework\TestCase;
use Src\Domain\Entities\StockItem;
use Src\Domain\Types\StockItemStatus;

class StockItemTest extends TestCase
{

    public function test_status_correctly_initialized(): void
    {
        $stockItem = new StockItem(1, 1000, 1000, 50);
        $this->assertEquals(StockItemStatus::INSTOCK, $stockItem->getStockStatus());

        $stockItem = new StockItem(1, 1000, 500, 50);
        $this->assertEquals(StockItemStatus::LOWSTOCK, $stockItem->getStockStatus());
    }

    public function test_consuming_larger_than_availability_not_allowed(): void
    {
        $stockItem = new StockItem(1, 1000, 500, 50);
        $canConsumeResult = $stockItem->canConsume(501);
        $this->assertEquals(false, $canConsumeResult);
        $this->assertEquals(500, $stockItem->getAvailableQuantity());

    }

    public function test_consuming_within_availability_allowed(): void
    {
        $stockItem = new StockItem(1, 1000, 1000, 50);
        $canConsumeResult = $stockItem->canConsume(20);
        $this->assertEquals(true, $canConsumeResult);
        $this->assertEquals(1000, $stockItem->getAvailableQuantity());
    }

    public function test_consuming_deduct_from_available(): void
    {
        $stockItem = new StockItem(1, 1000, 1000, 50);
        $consumeResult = $stockItem->consume(20);
        $this->assertEquals(StockItemStatus::INSTOCK, $consumeResult);
        $this->assertEquals(1000 - 20, $stockItem->getAvailableQuantity());
    }
}
