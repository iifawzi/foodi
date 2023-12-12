<?php

namespace Src\Domain\Services;

use Src\Domain\Entities\Merchant;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\StockItem;
use Src\Domain\Types\OrderStatus;

class CreateOrderUseCase
{
    /**
     * @param Merchant $merchant
     * @param Order $order
     * @param StockItem[] $stockItems
     * @return bool
     */
    public function confirmOrder(Merchant $merchant, Order $order, array $stockItems): bool
    {
        $status = $this->hasEnoughStock($order, $stockItems);
        if (!$status) {
            $order->setStatus(OrderStatus::CANCELLED);
            return false;
        }

        $orderIngredients = $order->getIngredients();
        foreach ($stockItems as $stock) {
            $statusBeforeConsuming = $stock->getStockStatus();
            $stock->consume($orderIngredients[$stock->getId()], $order->getId());
            if ($stock->getStockStatus() != $statusBeforeConsuming) {
                $merchant->notifyAboutStock($stock);
            }
        }
        $order->setStatus(OrderStatus::CONFIRMED);
        return true;
    }

    /**
     * @param Order $order,
     * @param StockItem[] $stockItems
     */
    private function hasEnoughStock(Order $order, array $stockItems): bool
    {
        $status = true;
        $orderIngredients = $order->getIngredients();
        foreach ($stockItems as $stock) {
            $canConsume = $stock->canConsume($orderIngredients[$stock->getId()]);
            if (!$canConsume) {
                $status = false;
                break;
            }
        }
        return $status;
    }

}
