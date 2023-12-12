<?php

namespace Src\Application\ports\infrastructure;
use Src\Domain\Entities\Order;

interface OrderRepository
{
    public function saveOrder(Order $order): void;
    public function startTransaction(): void;
    public function endTransaction(): void;
}
