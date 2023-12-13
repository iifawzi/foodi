<?php

namespace Src\Application\ports\infrastructure;

use Ramsey\Uuid\UuidInterface;
use Src\Domain\Entities\StockItem;
use Src\Infrastructure\types\LowStockNotificationType;

interface StockNotificationService
{
    /**
     * @param array{
     *      notification_id: UuidInterface,
     *      status: LowStockNotificationType,
     *      ingredient_id: int
     *  }[] $notifications
     * /
     */
    public function notifyLowThresholdStock(array $notifications): void;
}
