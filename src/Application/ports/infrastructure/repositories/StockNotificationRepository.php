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
     *  }[] $notifications
     * /
     */
    public function save(array $notifications): void;

    /**
     * @return array<int>
     */
    public function getStuckNotifications(): array;

    /**
     * @param array<int> $notificationIds
     */
    public function markSent(array $notificationIds): void;

    /**
     * @param array<int> $notificationIds
     * @return array{
     *       notification_id: UuidInterface,
     *       status: LowStockNotificationType,
     *       ingredient_id: int
     *   }[]
     */
    public function getPendingWithIds(array $notificationIds): array;
}
