<?php

namespace App\Jobs;

use App\Models\LowStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Src\Infrastructure\types\LowStockNotificationType;

class SendLowStockNotification implements ShouldQueue
{
    public int $tries = 3;
    public array $backoff = [60, 180];

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $id)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): bool
    {
        ["items" => $items, "email" => $email, "name" => $merchantName] = $this->getTheNotification();
        if (count($items)) {
            foreach ($items as $item) {
                Mail::to($email)->send(new \App\Mail\LowStockNotification($item, $merchantName));
            }
        }
        return true;
    }


    private function getTheNotification(): array
    {
        $notifications = LowStockNotification::with(['ingredientStock', 'ingredientStock.merchant'])
            ->whereIn('notification_id', $this->id)
            ->where('status', LowStockNotificationType::PENDING)
            ->get();

        if (count($notifications)) {
            // so they're not pulled again by the scheduler
            LowStockNotification::query()->whereIn('notification_id', $this->id)
                ->where('status', LowStockNotificationType::PENDING)
                ->update(['status' => LowStockNotificationType::QUEUED]);
        }


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
