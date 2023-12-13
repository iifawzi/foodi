<?php

namespace Src\Infrastructure\repositories\Eloquent;

use App\Models\LowStockNotification;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Infrastructure\types\LowStockNotificationType;

class EloquentStockNotificationRepository implements StockNotificationRepository
{
    public function save(array $notifications): void
    {
        $dataToInsert = [];
        foreach ($notifications as $notification) {
            $dataToInsert[] = [
                'notification_id' => $notification["notification_id"],
                'ingredient_id' => $notification["ingredient_id"],
                "status" => $notification["status"],
            ];
        }
        LowStockNotification::factory()->createMany($dataToInsert);
    }
}
