<?php

namespace Tests\Unit\Domain\Entities;
use Src\Domain\Entities\Merchant;
use Src\Domain\Entities\StockItem;
use Src\Infrastructure\types\LowStockNotificationType;
use Tests\TestCase;

class MerchantTest extends TestCase
{
    public function test_merchant_initialized_correctly(): void
    {
        $merchant = new Merchant(1, 'Fawzi', 'iifawzie@gmail.com');
        $this->assertEquals(1, $merchant->getId());
        $this->assertEquals('Fawzi', $merchant->getName());
        $this->assertEquals('iifawzie@gmail.com', $merchant->getEmail());
    }
    public function test_stocks_notifications_stored_correctly(): void
    {
        $merchant = new Merchant(1, 'Fawzi', 'iifawzie@gmail.com');
        $stock = new StockItem(1, 'Beef', 100, 40, 50);
        $merchant->notifyAboutStock($stock);

        $notification = $merchant->getNotifications()[0];
        $this->assertEquals(LowStockNotificationType::PENDING, $notification["status"]);
        $this->assertEquals($stock->getId(), $notification["ingredient_id"]);
    }
}
