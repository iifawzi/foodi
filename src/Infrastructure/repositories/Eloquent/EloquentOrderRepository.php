<?php

namespace Src\Infrastructure\repositories\Eloquent;

use App\Models\OrderItem;
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
                "order_id" => $order->getId()->toString()
            ];
        }


        /**
 * @var \App\Models\Order $orderModel
*/
         \App\Models\Order::create(
            [
            "order_id" => $order->getId()->toString(),
            "status" => $order->getStatus(),
            ]
        );
        OrderItem::insert($items);
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
