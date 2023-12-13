<?php

namespace Src\Application\ports\infrastructure\repositories;

use App\Models\LowStockNotification;
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
     * @return array<string>
     */
    public function getStuckNotifications(): array;

    /**
     * @param array<string> $notificationIds
     */
    public function markSent(array $notificationIds): void;

    /**
     * @param array<string> $notificationIds
     * @return LowStockNotification[]
     */
    public function getPendingWithIds(array $notificationIds): array;
}
