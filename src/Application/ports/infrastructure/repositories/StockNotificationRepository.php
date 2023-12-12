<?php

namespace Src\Application\ports\infrastructure\repositories;

use Src\Domain\Entities\StockItem;

interface StockNotificationRepository
{
    /**
     * @var StockItem[] $stockItems
     */
    public function save(array $stockItems): void;
}
