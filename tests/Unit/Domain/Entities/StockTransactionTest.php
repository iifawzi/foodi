<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use Src\Domain\Entities\StockTransaction;
use Src\Domain\Types\StockTransactionType;

class StockTransactionTest extends TestCase
{
    public function test_initialized_with_uuid_and_correct_state(): void
    {
        $stockTransaction = new StockTransaction(1, 1000, "for order test");
        $this->assertNotNull($stockTransaction->getId());
        $this->assertEquals(StockTransactionType::OUT, $stockTransaction->getType());
    }
}
