<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;

class SendLowStockNotification implements ShouldQueue
{
    public int $tries = 3;

    /**
     * @var array|int[]
     */
    public array $backoff = [60, 180];

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string[] $ids
     */
    public function __construct(public readonly array $ids)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(StockNotificationRepository $stockNotificationRepository): bool
    {
        ["items" => $items, "email" => $email, "name" => $merchantName] = $this->getTheNotification($stockNotificationRepository);

        if (count($items)) {
            foreach ($items as $item) {
                Mail::to($email)->send(new \App\Mail\LowStockNotification($item, $merchantName));
            }
            $stockNotificationRepository->markSent($this->ids);
        }
        return true;
    }


    /**
     * @param StockNotificationRepository $stockNotificationRepository
     * @return array{
     *     items: array{
     *         ingredient_id: int,
     *         ingredient_name: string,
     *         threshold: int,
     *         current: int,
     *     }[] ,
     *    email: string,
     *    name: string
     * }
     */
    private function getTheNotification(StockNotificationRepository $stockNotificationRepository): array
    {
        $notifications = $stockNotificationRepository->getPendingWithIds($this->ids);
        $merchantName = null;
        $merchantEmail = null;
        $items = [];
        foreach ($notifications as $notification) {
            $ingredientStock = $notification->ingredientStock;
            $ingredientName = $ingredientStock->name;
            $ingredientId = $ingredientStock->ingredient_id;
            $available = $ingredientStock->available_quantity;
            $full = $ingredientStock->full_quantity;
            $min_percentage = $ingredientStock->min_threshold_percentage;
            $threshold = $full * ($min_percentage / 100);

            if (!isset($merchantName)) {
                $merchantName = $ingredientStock->merchant->name;
                $merchantEmail = $ingredientStock->merchant->email;
            }

            if ($available > $threshold) {
              continue;
            }
            $items[] = [
                "ingredient_id" => $ingredientId,
                "ingredient_name" => $ingredientName,
                "threshold" => $threshold,
                "current" => $available
            ];
        }
        return ["items" => $items, "email" => $merchantEmail, "name" => $merchantName];
    }
}
