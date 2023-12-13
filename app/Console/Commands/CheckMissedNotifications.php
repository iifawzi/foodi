<?php

namespace App\Console\Commands;

use App\Jobs\SendLowStockNotification;
use App\Models\LowStockNotification;
use Illuminate\Console\Command;
use Src\Infrastructure\types\LowStockNotificationType;
use Carbon\Carbon;

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
    protected $description = 'Queuing the missed to be queued notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationIds = LowStockNotification::query()
            ->where("status", LowStockNotificationType::PENDING)
            ->where('created_at', '<=', Carbon::now('UTC')->subMinutes(30))
            ->pluck('notification_id')
            ->toArray();
        SendLowStockNotification::dispatch($notificationIds);
    }
}
