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
        $this->assertEquals(StockItemStatus::INSTOCK,$stockItem->getStockStatus());

        $stockItem = new StockItem(1, 1000, 500, 50);
        $this->assertEquals(StockItemStatus::LOWSTOCK,$stockItem->getStockStatus());
    }

    public function test_consuming_larger_than_availability_not_allowed(): void {
        $stockItem = new StockItem(1, 1000, 500, 50);
        $consumeResult = $stockItem->consume(501);
        $this->assertEquals(StockItemStatus::OUTSTOCK, $consumeResult);
    }

    public function test_consuming_within_availability_allowed_and_update_states(): void {
        $stockItem = new StockItem(1, 1000, 1000, 50);
        $consumeResult = $stockItem->consume(20);
        $this->assertEquals(StockItemStatus::INSTOCK, $consumeResult);
        $this->assertEquals(1000 - 20,$stockItem->getAvailableQuantity());
    }
}
