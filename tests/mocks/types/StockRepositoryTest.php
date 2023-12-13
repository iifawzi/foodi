<?php

namespace Tests\mocks\types;

use Src\Application\ports\infrastructure\repositories\StockRepository;
use Src\Domain\Entities\StockItem;

interface StockRepositoryTest extends StockRepository
{
    public function getStockTransactions(int $ingredientId): array;

    public function getStockItem(int $ingredientId): StockItem;

    /**
     * @param array $ingredientIds
     * @return array|\Src\Domain\Entities\StockItem[]
     */
    public function getStockItems(array $ingredientIds): array;
}
