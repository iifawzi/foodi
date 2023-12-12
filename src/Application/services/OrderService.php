<?php

namespace Src\Application\services;

use mysql_xdevapi\Exception;
use Src\Application\ports\infrastructure\repositories\MerchantRepository;
use Src\Application\ports\infrastructure\repositories\OrderRepository;
use Src\Application\ports\infrastructure\repositories\ProductRepository;
use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Application\ports\infrastructure\repositories\StockRepository;
use Src\Application\ports\infrastructure\StockNotificationService;
use Src\Domain\Entities\Item;
use Src\Domain\Entities\Order;
use Src\Domain\Services\OrderUseCases;

class OrderService
{
    public function __construct(
        private readonly MerchantRepository          $merchantRepository,
        private readonly OrderRepository             $orderRepository,
        private readonly ProductRepository           $productRepository,
        private readonly StockRepository             $stockRepository,
        private readonly OrderUseCases               $orderUseCases,
        private readonly StockNotificationRepository $stockNotificationRepository,
        private readonly StockNotificationService $stockNotificationService,
    )
    {
    }

    /**
     * @param array $order
     * @return array{
     *     "status": bool,
     *     "order": Order
     * }
     */
    public function CreateOrder(array $order): array
    {
        try {
        $productQuantities = collect($order["products"])->pluck('quantity', 'product_id');

        $merchant = $this->merchantRepository->getMerchant(data_get($order, 'merchantId', 1));
        $items = $this->productRepository->getItems($productQuantities);

        $order = new Order();
        $order->addItems($items);
        $uniqueIngredientIds = $this->getUniqueIngredientIds($items);

        $this->orderRepository->startTransaction();
        $stockItems = $this->stockRepository->getStockItems($uniqueIngredientIds);
        $isConfirmed = $this->orderUseCases->confirmOrder($merchant, $order, $stockItems);

        if (!$isConfirmed) {
            return ["status" => false, "order" => $order];
        }

        $this->stockRepository->updateStocks($stockItems);

        $stocksToRefill = $merchant->getItemsToRefill();

        if (count($stocksToRefill)) {
            $this->stockNotificationRepository->save($stocksToRefill);
        }

        $this->orderRepository->saveOrder($order);
        $this->orderRepository->commitTransaction();

        if (count($stocksToRefill)) {
            $this->stockNotificationService->notifyLowThresholdStock($stocksToRefill);
        }

        return ["status" => true, "order" => $order];
        } catch (\Exception $e) {
            report($e);
            $this->orderRepository->rollbackTransaction();
            return ["status" => false, "order" => $order];
        }
    }


    /**
     * @param array<Item> $items
     * @return array<int>
     */
    private function getUniqueIngredientIds(array $items): array
    {
        $uniqueIds = [];
        foreach ($items as $item) {
            $ingredients = $item->getIngredients();
            foreach ($ingredients as $ingredient) {
                $ingredientId = $ingredient->getId();
                if (!in_array($ingredientId, $uniqueIds)) {
                    $uniqueIds[] = $ingredientId;
                }
            }
        }
        return $uniqueIds;
    }
}
