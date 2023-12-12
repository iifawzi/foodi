<?php

namespace Tests\mocks;

use Src\Application\ports\infrastructure\OrderRepository;
use Src\Domain\Entities\Order;

class MockedOrderRepository implements OrderRepository
{
    /**
     * @var Order[] $data;
     */
    private array $data;
    public function saveOrder(Order $order): void
    {
       $this->data[] = $order;
    }

    public function startTransaction(): void
    {
        // TODO: Implement startTransaction() method.
    }

    public function endTransaction(): void
    {
        // TODO: Implement endTransaction() method.
    }
}
