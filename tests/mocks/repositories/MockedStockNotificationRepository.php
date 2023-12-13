<?php

namespace Tests\mocks\repositories;

use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Domain\Entities\StockItem;
use Src\Infrastructure\types\LowStockNotificationType;

class MockedStockNotificationRepository implements StockNotificationRepository
{
    private array $data = [];

    /**
     * @param StockItem[] $stockItems
     * @return void
     */
    public function save(array $stockItems): void
    {
        foreach ($stockItems as $stockItem) {
            $this->data[] = [
                "ingredientId" => $stockItem->getId(),
                "status" => LowStockNotificationType::PENDING,
            ];
        }
    }

    public function getNotifications(): array
    {
        return $this->data;
    }
}
