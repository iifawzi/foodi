<?php

namespace Src\Application\ports\infrastructure\repositories;
use Src\Domain\Entities\StockItem;

interface StockRepository
{
    /**
     * @param  array<int> $ingredientIds
     * @return StockItem[]
     */
    public function getStockItems(array $ingredientIds): array;

    /**
     * @param StockItem[] $stockItems
     */
    public function updateStocks(array $stockItems): void;

}
