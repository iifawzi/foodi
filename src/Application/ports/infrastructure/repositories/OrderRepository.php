<?php

namespace Src\Application\ports\infrastructure\repositories;
use Src\Domain\Entities\Order;

interface OrderRepository
{
    public function saveOrder(Order $order): void;
    public function startTransaction(): void;
    public function commitTransaction(): void;
    public function rollbackTransaction(): void;
}
