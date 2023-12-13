<?php

namespace Tests\mocks\repositories;

use Ramsey\Uuid\UuidInterface;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Domain\Entities\StockItem;
use Src\Infrastructure\types\LowStockNotificationType;

class MockedStockNotificationRepository implements StockNotificationRepository
{
    private array $data = [];

    /**
     * @param array{
     *      notification_id: UuidInterface,
     *      status: LowStockNotificationType,
     *      ingredient_id: int
     *  } $notifications
     * /
     * /
     */
    public function save(array $notifications): void
    {
        foreach ($notifications as $notification) {
            $this->data[] = [
                "notification_id" => $notification["notification_id"],
                "ingredient_id" => $notification["ingredient_id"],
                "status" => $notification["ingredient_id"],
            ];
        }
    }

    public function getNotifications(): array
    {
        return $this->data;
    }

    public function getStuckNotifications(): array
    {
        // TESTED ON E2E.
        return [];
    }

    public function markSent(array $notificationIds): void
    {
        // TESTED ON E2E.
    }

    public function getPendingWithIds(array $notificationIds): array
    {
        // TESTED ON E2E.
        return [];
    }
}
