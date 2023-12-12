<?php

namespace Tests\Unit\Domain\Entities;
use Src\Domain\Entities\Merchant;
use Src\Domain\Entities\StockItem;
use Tests\TestCase;

class MerchantTest extends TestCase
{
    public function test_stocks_notifications_stored_correctly(): void
    {
        $merchant = new Merchant(1, 'Fawzi', 'iifawzie@gmail.com');
        $stock = new StockItem(1, 'Beef', 100, 40, 50);
        $merchant->notifyAboutStock($stock);

        $this->assertEquals([$stock], $merchant->getItemsToRefill());

    }
}
