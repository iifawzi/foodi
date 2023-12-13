<?php

namespace App\Console\Commands;

use App\Jobs\SendLowStockNotification;
use Illuminate\Console\Command;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;

class CheckMissedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-missed-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queuing unsent notifications';

    /**
     * Execute the console command.
     */
    public function handle(StockNotificationRepository $stockNotificationRepository): void
    {
        $notificationIds = $stockNotificationRepository->getStuckNotifications();
        if (count($notificationIds)) {
            SendLowStockNotification::dispatch($notificationIds);
        }
    }
}
