<?php

namespace Src\Application\ports\infrastructure;

use Src\Domain\Entities\StockItem;

interface StockNotificationRepository
{
    /**
     * @var StockItem[] $stockItems
     */
    public function save(array $stockItems): void;
}
