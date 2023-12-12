<?php

namespace Src\Infrastructure\repositories\Eloquent;

use App\Models\LowStockNotification;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;

class EloquentStockNotificationRepository implements StockNotificationRepository
{
    public function save(array $stockItems): void
    {
        $dataToInsert = [];
        foreach ($stockItems as $stock) {
            $dataToInsert[] = [
                'threshold' => $stock->getThresholdLimit(),
                'ingredient_id' => $stock->getId(),
                "merchant_id" => $stock->getMerchantId()
            ];
        }
        LowStockNotification::factory()->createMany($dataToInsert);
    }
}
