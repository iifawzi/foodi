<?php

namespace Src\Infrastructure;

use App\Jobs\SendLowStockNotification;
use Src\Application\ports\infrastructure\StockNotificationService;

class MailingService implements StockNotificationService
{

    public function notifyLowThresholdStock(array $notifications): void
    {
        $ids = [];
        foreach ($notifications as $notification) {
            $ids[] = $notification["notification_id"];
        }
        SendLowStockNotification::dispatch($ids);
    }
}
