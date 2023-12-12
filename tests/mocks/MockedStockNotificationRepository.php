<?php

namespace Tests\mocks;

use Src\Application\ports\infrastructure\StockNotificationRepository;
use Src\Domain\Entities\StockItem;

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
            $this->data[] = ["ingredientId" => $stockItem->getId(), "name" => $stockItem->getName(), "exceeded" => $stockItem->getThresholdLimit()];
        }
    }

    public function getNotifications(): array
    {
        return $this->data;
    }
}
