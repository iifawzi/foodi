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
                'ingredient_id' => $stock->getId(),
                "status" => "PENDING",
            ];
        }
        LowStockNotification::factory()->createMany($dataToInsert);
    }
}
