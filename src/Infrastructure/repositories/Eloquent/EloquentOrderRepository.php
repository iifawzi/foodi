<?php

namespace Src\Infrastructure\repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Application\ports\infrastructure\repositories\OrderRepository;
use Src\Domain\Entities\Order;

class EloquentOrderRepository implements OrderRepository
{
    public function saveOrder(Order $order): void
    {
        $items = [];

        foreach ($order->getItems() as $item) {
            $items[] = [
                "quantity" => $item->getQuantity(),
                "unit_price" => $item->getUnitPrice(),
                "total_price" => $item->getTotalPrice(),
                "product_id" => $item->getId(),
            ];
        }


        /**
 * @var \App\Models\Order $orderModel
*/
        $orderModel = \App\Models\Order::factory()->createOne(
            [
            "order_id" => $order->getId()->toString(),
            "status" => $order->getStatus(),
            ]
        );

        $orderModel->items()->createMany($items);
    }

    public function startTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commitTransaction(): void
    {
        DB::commit();
    }

    public function rollbackTransaction(): void
    {
        DB::rollBack();
    }
}
