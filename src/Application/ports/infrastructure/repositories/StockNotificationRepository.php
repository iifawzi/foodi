<?php

namespace Src\Application\ports\infrastructure\repositories;

use Ramsey\Uuid\UuidInterface;
use Src\Infrastructure\types\LowStockNotificationType;

interface StockNotificationRepository
{
    /**
     * @param array{
     *      notification_id: UuidInterface,
     *      status: LowStockNotificationType,
     *      ingredient_id: int
     *  } $notifications
     * /
     */
    public function save(array $notifications): void;
    public function getStuckNotifications(): array;
    public function markSent(array $notificationIds): void;
    public function getPendingWithIds(array $notificationIds): array;
}
