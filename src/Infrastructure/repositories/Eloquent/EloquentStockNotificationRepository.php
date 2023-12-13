<?php

namespace Src\Infrastructure\repositories\Eloquent;

use App\Models\LowStockNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\UuidInterface;
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

    /**
     * @return array<int>
     */
    public function getStuckNotifications(): array
    {
        DB::beginTransaction();
        $notificationIds = LowStockNotification::query()
            ->where("status", LowStockNotificationType::PENDING)
            ->where('updated_at', '<=', Carbon::now('UTC')->subMinutes(30))
            ->pluck('notification_id')
            ->toArray();

        if (count($notificationIds)) {
            LowStockNotification::query()
                ->whereIn('notification_id', $notificationIds)
                ->where("status", LowStockNotificationType::PENDING)
                ->update(['updated_at' => Carbon::now('UTC')]);
        }
        DB::commit();
        return $notificationIds;
    }

    public function getPendingWithIds($notificationIds): array
    {
        return LowStockNotification::with(['ingredientStock', 'ingredientStock.merchant'])
            ->whereIn('notification_id', $notificationIds)
            ->where('status', LowStockNotificationType::PENDING)
            ->get()->all();
    }

    public function markSent(array $notificationIds): void
    {
        LowStockNotification::query()->whereIn('notification_id', $notificationIds)
        ->where('status', LowStockNotificationType::PENDING)
        ->update(['status' => LowStockNotificationType::SENT]);
    }
}
