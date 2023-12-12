<?php

namespace Tests\mocks\repositories;

use Src\Application\ports\infrastructure\repositories\OrderRepository;
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

    public function commitTransaction(): void
    {
        // TODO: Implement commitTransaction() method.
    }

    public function rollbackTransaction(): void
    {
        // TODO: Implement rollbackTransaction() method.
    }
}
