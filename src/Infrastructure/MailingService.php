<?php

namespace Src\Infrastructure;

use App\Jobs\SendLowStockNotification;
use Src\Application\ports\infrastructure\StockNotificationService;

class MailingService implements StockNotificationService
{

    public function notifyLowThresholdStock(array $stocksItems): void
    {
        $ids = [];
        foreach ($stocksItems as $stocksItem) {
            $ids[] = $stocksItem->getId();
        }
        SendLowStockNotification::dispatch($ids);
    }
}
