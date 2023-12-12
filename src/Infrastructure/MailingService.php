<?php

namespace Src\Infrastructure;

use Src\Application\ports\infrastructure\StockNotificationService;

class MailingService implements StockNotificationService
{

    public function notifyLowThresholdStock(array $stocksItems): void
    {
        // TODO: Implement notifyLowThresholdStock() method.
    }
}
