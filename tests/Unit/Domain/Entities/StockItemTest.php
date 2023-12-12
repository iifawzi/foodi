<?php

namespace Tests\Unit\Domain\Entities;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Src\Domain\Entities\StockItem;
use Src\Domain\Types\StockItemStatus;

class StockItemTest extends TestCase
{

    public function test_status_correctly_initialized(): void
    {
        $stockItem = new StockItem(1, 'beef', 1000, 1000, 50);
        $this->assertEquals(StockItemStatus::INSTOCK, $stockItem->getStockStatus());
        $this->assertEquals(500, $stockItem->getThresholdLimit());

        $stockItem1 = new StockItem(1, 'cheese', 1000, 500, 50);
        $this->assertEquals(StockItemStatus::LOWSTOCK, $stockItem1->getStockStatus());
        $this->assertEquals(500, $stockItem1->getThresholdLimit());

    }

    public function test_consuming_larger_than_availability_is_not_allowed(): void
    {
        $stockItem = new StockItem(1, 'beef', 1000, 500, 50);
        $canConsumeResult = $stockItem->canConsume(501);
        $this->assertEquals(false, $canConsumeResult);
        $this->assertEquals(500, $stockItem->getAvailableQuantity());

    }

    public function test_consuming_within_availability_is_allowed(): void
    {
        $stockItem = new StockItem(1, 'beef', 1000, 1000, 50);
        $canConsumeResult = $stockItem->canConsume(20);
        $this->assertEquals(true, $canConsumeResult);
        $this->assertEquals(1000, $stockItem->getAvailableQuantity());
    }

    public function test_consuming_deduct_from_available_and_log_transaction(): void
    {
        $stockItem = new StockItem(1, 'beef', 1000, 1000, 50);
        $orderId = Str::uuid();
        $consumeResult = $stockItem->consume(20, $orderId);
        $this->assertEquals(StockItemStatus::INSTOCK, $consumeResult);
        $this->assertEquals(1000 - 20, $stockItem->getAvailableQuantity());


        $transaction = $stockItem->getStockTransactions()[0];
        $this->assertEquals(20,$transaction->getQuantity());
        $this->assertEquals('order: ' . $orderId, $transaction->getReason());
        $this->assertNotNull($transaction->getId());
    }
}
