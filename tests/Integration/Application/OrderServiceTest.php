<?php

namespace Integration\Application;

use Mockery\MockInterface;
use Src\Application\ports\infrastructure\repositories\MerchantRepository;
use Src\Application\ports\infrastructure\repositories\OrderRepository;
use Src\Application\ports\infrastructure\repositories\ProductRepository;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Application\ports\infrastructure\repositories\StockRepository;
use Src\Application\ports\infrastructure\StockNotificationService;
use Src\Domain\Types\OrderStatus;
use Tests\mocks\repositories\MockedMerchantRepository;
use Tests\mocks\repositories\MockedOrderRepository;
use Tests\mocks\repositories\MockedProductRepository;
use Tests\mocks\repositories\MockedStockNotificationRepository;
use Tests\mocks\repositories\MockedStockRepository;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    public \Src\Application\services\OrderService $orderService;
    public StockRepository $stockRepository;
    public OrderRepository $orderRepository;
    public StockNotificationRepository $stockNotificationRepository;
    public MockInterface $stockNotificationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->bindRepositories();
        $this->orderService = $this->app->make(\Src\Application\services\OrderService::class);
        $this->stockRepository = $this->app->make(StockRepository::class);
        $this->orderRepository = $this->app->make(OrderRepository::class);
        $this->stockNotificationRepository = $this->app->make(StockNotificationRepository::class);
    }

    private function bindRepositories(): void
    {
        $this->app->singleton(MerchantRepository::class, MockedMerchantRepository::class);
        $this->app->singleton(OrderRepository::class, MockedOrderRepository::class);
        $this->app->singleton(ProductRepository::class, MockedProductRepository::class);
        $this->app->singleton(StockRepository::class, MockedStockRepository::class);
        $this->app->singleton(StockNotificationRepository::class, MockedStockNotificationRepository::class);

        $this->stockNotificationService = \Mockery::mock(StockNotificationService::class);
        $this->instance(
            StockNotificationService::class,
            $this->stockNotificationService
        );
    }

    public function test_order_is_confirmed_and_stock_deducted_and_logged()
    {
        /**
         * product one consists of 3 ingredients
         * 1- beef 150g -- full quantity 2000
         * 2- cheese 30g -- full quantity 500
         * 3- Onion 20g -- full quantity 200
         * check mocked product and stock for mocks.
         */
        $request = [
            "merchantId" => 1,
            "products" => [
                ["product_id" => 1, "quantity" => 2],
            ],
        ];
        ["status" => $status, "order" => $order] = $this->orderService->CreateOrder($request);

        $this->assertTrue($status);
        $this->assertEquals(OrderStatus::CONFIRMED, $order->getStatus());

        // STOCKS VERIFICATION
        $stock1AfterOrder = $this->stockRepository->getStockItem(1);
        $stock2AfterOrder = $this->stockRepository->getStockItem(2);
        $stock3AfterOrder = $this->stockRepository->getStockItem(3);

        $this->assertEquals(1700, $stock1AfterOrder->getAvailableQuantity());
        $this->assertEquals(440, $stock2AfterOrder->getAvailableQuantity());
        $this->assertEquals(160, $stock3AfterOrder->getAvailableQuantity());

        // TRANSACTIONS VERIFICATION
        $stock1Transaction = $this->stockRepository->getStockTransactions(1);
        $this->assertCount(1, $stock1Transaction);
        $this->assertEquals(1, $stock1Transaction[0]->getIngredientId());
        $this->assertEquals(300, $stock1Transaction[0]->getQuantity());

        $stock2Transaction = $this->stockRepository->getStockTransactions(2);
        $this->assertCount(1, $stock2Transaction);
        $this->assertEquals(2, $stock2Transaction[0]->getIngredientId());
        $this->assertEquals(60, $stock2Transaction[0]->getQuantity());

        $stock3Transaction = $this->stockRepository->getStockTransactions(3);
        $this->assertCount(1, $stock3Transaction);
        $this->assertEquals(3, $stock3Transaction[0]->getIngredientId());
        $this->assertEquals(40, $stock3Transaction[0]->getQuantity());
    }
    public function test_order_with_multiple_products_is_confirmed_and_stock_deducted_and_logged()
    {
        /**
         * product one consists of 3 ingredients
         * 1- beef 150g -- full stock quantity 1000
         * 2- cheese 30g -- full stock quantity 500
         * 3- Onion 20g -- full stock quantity 200
         *
         *  product one consists of the same 3 ingredients,
         *  but it's the double ( for people like me who loves eating)
         *  1- beef 300g
         *  2- cheese 60g
         *  3- Onion 30g
         *
         * check mocked product and stock for mocks.
         */
        $request = [
            "merchantId" => 1,
            "products" => [
                ["product_id" => 1, "quantity" => 2],
                ["product_id" => 2, "quantity" => 1]
            ],
        ];
        ["status" => $status, "order" => $order] = $this->orderService->CreateOrder($request);

        $this->assertTrue($status);
        $this->assertEquals(OrderStatus::CONFIRMED, $order->getStatus());

        // STOCKS VERIFICATION
        $stock1AfterOrder = $this->stockRepository->getStockItem(1);
        $stock2AfterOrder = $this->stockRepository->getStockItem(2);
        $stock3AfterOrder = $this->stockRepository->getStockItem(3);

        // 600 should be conducted -> 300 for first order (150 for each burger) and 300 for second order.
        $this->assertEquals(1400, $stock1AfterOrder->getAvailableQuantity());
        // 120 should be conducted -> 60  for first order (30 for each burger) and 60 for second order.
        $this->assertEquals(380, $stock2AfterOrder->getAvailableQuantity());
        // 70 should be conducted -> 40  for first order (20 for each burger) and 30 for second order.
        $this->assertEquals(130, $stock3AfterOrder->getAvailableQuantity());

        // TRANSACTIONS VERIFICATION
        $stock1Transaction = $this->stockRepository->getStockTransactions(1);
        $this->assertCount(1, $stock1Transaction);
        $this->assertEquals(1, $stock1Transaction[0]->getIngredientId());
        $this->assertEquals(600, $stock1Transaction[0]->getQuantity());

        $stock2Transaction = $this->stockRepository->getStockTransactions(2);
        $this->assertCount(1, $stock2Transaction);
        $this->assertEquals(2, $stock2Transaction[0]->getIngredientId());
        $this->assertEquals(120, $stock2Transaction[0]->getQuantity());

        $stock3Transaction = $this->stockRepository->getStockTransactions(3);
        $this->assertCount(1, $stock3Transaction);
        $this->assertEquals(3, $stock3Transaction[0]->getIngredientId());
        $this->assertEquals(70, $stock3Transaction[0]->getQuantity());
    }
    public function test_order_cancelled_when_any_ingredient_out_of_stock()
    {
        /**
         * product one consists of 3 ingredients
         * 1- beef 150g -- full quantity 2000
         * 2- cheese 30g -- full quantity 500
         * 3- Onion 20g -- full quantity 200
         * check mocked product and stock for mocks.
         */
        $request = [
            "merchantId" => 1,
            "products" => [
                ["product_id" => 1, "quantity" => 11],
            ],
        ];

        ["status" => $status, "order" => $order] = $this->orderService->CreateOrder($request);
        // onion is out of stock, we can't serve 11 order.
        $this->assertFalse($status);
        $this->assertEquals(OrderStatus::CANCELLED, $order->getStatus());
    }
    public function test_order_confirmed_and_merchant_notified_for_low_stock_only_once_per_ingredient()
    {
        // after first order, the merchant will be notified of two ingredients running low (beef and onion)
        $this->stockNotificationService
            ->shouldReceive('notifyLowThresholdStock')
            ->times(1)
            ->withArgs(function ($arguments) {
                return count($arguments) == 2;
            });

        // after first order, the merchant will be notified of the third ingredient ( cheese )
        $this->stockNotificationService
            ->shouldReceive('notifyLowThresholdStock')
            ->times(1)
            ->withArgs(function ($arguments) {
                return count($arguments) == 1;
            });
        /**
         * product one consists of 3 ingredients
         * 1- beef 150g -- full quantity 2000
         * 2- cheese 30g -- full quantity 500
         * 3- Onion 20g -- full quantity 200
         * check mocked product and stock for mocks.
         */
        $request = [
            "merchantId" => 1,
            "products" => [
                ["product_id" => 1, "quantity" => 7],
            ],
        ];
        ["status" => $status, "order" => $order] = $this->orderService->CreateOrder($request);

        $this->assertTrue($status);
        $this->assertEquals(OrderStatus::CONFIRMED, $order->getStatus());

        // Onion and beef exceeded the threshold
        $notifications = $this->stockNotificationRepository->getNotifications();
        $this->assertCount(2, $notifications);
        $firstNotification = $notifications[0];
        $this->assertEquals(1, $firstNotification["ingredientId"]);
        $this->assertEquals(1000, $firstNotification["threshold"]);

        $secondNotification = $notifications[1];
        $this->assertEquals(3, $secondNotification["ingredientId"]);
        $this->assertEquals(100, $secondNotification["threshold"]);

        // CREATING ANOTHER ORDER TO VERIFY IT WON'T BE NOTIFIED.
        $orderRequest2 = [
            "merchantId" => 1,
            "products" => [
                ["product_id" => 1, "quantity" => 2],
            ],
        ];
        ["status" => $status2, "order" => $order2] = $this->orderService->CreateOrder($orderRequest2);
        $this->assertTrue($status2);
        $this->assertEquals(OrderStatus::CONFIRMED, $order2->getStatus());

        // the repository should now have three
        $notifications = $this->stockNotificationRepository->getNotifications();
        $this->assertCount(3, $notifications);
        $firstNotification = $notifications[0];
        $this->assertEquals(1, $firstNotification["ingredientId"]);
        $this->assertEquals(1000, $firstNotification["threshold"]);

        $secondNotification = $notifications[1];
        $this->assertEquals(3, $secondNotification["ingredientId"]);
        $this->assertEquals(100, $secondNotification["threshold"]);

        $secondNotification = $notifications[2];
        $this->assertEquals(2, $secondNotification["ingredientId"]);
        $this->assertEquals(250, $secondNotification["threshold"]);
    }
}
