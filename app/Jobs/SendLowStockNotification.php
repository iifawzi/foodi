<?php

namespace App\Jobs;

use App\Models\LowStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification implements ShouldQueue
{
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
        ["items" => $items, "email" => $email, "name" => $merchantName] = $this->getNotificationsToSend();
        if (count($items)) {
            Mail::to($email)->send(new \App\Mail\LowStockNotification($items, $merchantName));
        }
        return true;
    }


    private function getNotificationsToSend(): array
    {

        $notifications = LowStockNotification::with(['ingredientStock', 'ingredientStock.merchant'])
            ->whereIn('ingredient_id', $this->id)
            ->where('status', 'PENDING')
            ->get();

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
            $threshold = $full * $min_percentage;

            if (!isset($merchantName)) {
                $merchantName = $ingredientStock->merchant->name;
                $merchantEmail = $ingredientStock->merchant->email;
            }

            if ($available > $threshold) {
              continue;
            }
            $items[] = [
                "ingredientId" => $ingredientId,
                "ingredientName" => $ingredientName,
                "threshold" => $threshold,
                "current" => $available
            ];
        }
        return ["items" => $items, "email" => $merchantEmail, "name" => $merchantName];
    }
}
