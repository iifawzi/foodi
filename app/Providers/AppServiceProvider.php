<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Application\ports\infrastructure\repositories\MerchantRepository;
use Src\Application\ports\infrastructure\repositories\OrderRepository;
use Src\Application\ports\infrastructure\repositories\ProductRepository;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Application\ports\infrastructure\repositories\StockRepository;
use Src\Application\ports\infrastructure\StockNotificationService;
use Src\Infrastructure\MailingService;
use Src\Infrastructure\repositories\Eloquent\EloquentMerchantRepository;
use Src\Infrastructure\repositories\Eloquent\EloquentOrderRepository;
use Src\Infrastructure\repositories\Eloquent\EloquentProductRepository;
use Src\Infrastructure\repositories\Eloquent\EloquentStockNotificationRepository;
use Src\Infrastructure\repositories\Eloquent\EloquentStockRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Injecting the repositories
        $this->app->bind(MerchantRepository::class, EloquentMerchantRepository::class);
        $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(StockNotificationRepository::class, EloquentStockNotificationRepository::class);
        $this->app->bind(StockRepository::class, EloquentStockRepository::class);
        // injecting the mailing service
        $this->app->bind(StockNotificationService::class, MailingService::class);
    }
}
