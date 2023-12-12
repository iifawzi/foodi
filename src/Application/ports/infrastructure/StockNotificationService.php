<?php

namespace Src\Application\ports\infrastructure;

use Src\Domain\Entities\StockItem;

interface StockNotificationService
{
    /**
     * @param StockItem[] $stocksItems
     */
    public function notifyLowThresholdStock(array $stocksItems): void;
}
