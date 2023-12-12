<?php

namespace Tests\mocks;

use Src\Application\ports\infrastructure\StockRepository;
use Src\Domain\Entities\StockItem;
use Src\Domain\Entities\StockTransaction;

class MockedStockRepository implements StockRepository
{

    private array $data;

    public function __construct()
    {
        $this->data = [
            1=> ["id" => 1, "name" => "Beef", "description" => 'Beef 1', "fullQuantity" => 2000, "availableQuantity" => 2000, "thresholdPercentage" => 50, "stockTransactions" => []],
            2=> ["id" => 2, "name" => "Cheese", "description" => 'Cheese 1', "fullQuantity" => 500, "availableQuantity" => 500, "thresholdPercentage" => 50, "stockTransactions" => []],
            3=> ["id" => 3, "name" => "Onion", "description" => 'Onion 1', "fullQuantity" => 200, "availableQuantity" => 200, "thresholdPercentage" => 50, "stockTransactions" => []],
        ];
    }

    /**
     * @param array $ingredientIds
     * @return array|\Src\Domain\Entities\StockItem[]
     */
    public function getStockItems(array $ingredientIds): array
    {
        $stockItems = [];
        foreach ($ingredientIds as $ingredientId) {
            $ingredientData = $this->data[$ingredientId];
            $stockItems[] = new StockItem($ingredientData["id"], $ingredientData["name"], $ingredientData["fullQuantity"], $ingredientData["availableQuantity"], $ingredientData["thresholdPercentage"]);
        }
        return $stockItems;
    }

    public function getStockItem(int $ingredientId): StockItem {
        $ingredientData = $this->data[$ingredientId];
        return new StockItem($ingredientData["id"], $ingredientData["name"], $ingredientData["fullQuantity"], $ingredientData["availableQuantity"], $ingredientData["thresholdPercentage"]);
    }

    /**
     * @param int $ingredientId
     * @return StockTransaction[]
     */
    public function getStockTransactions(int $ingredientId): array {
        $transactions = [];
        $IngredientTransactions = $this->data[$ingredientId]["stockTransactions"];
        foreach ($IngredientTransactions as $transaction) {
            $transactions[] = new StockTransaction($transaction["ingredientId"], $transaction["quantity"], $transaction["reason"]);
        }
        return $transactions;
    }

    /**
     * @param StockItem[] $stockItems
     */
    public function updateStocks(array $stockItems): void
    {
        foreach ($stockItems as $stock) {
            $this->data[$stock->getId()]["availableQuantity"] = $stock->getAvailableQuantity();

            $stockTransactions = $stock->getStockTransactions();
            if (count($stockTransactions)) {
                foreach ($stockTransactions as $transaction) {
                    $this->data[$stock->getId()]["stockTransactions"][] =
                        [
                            "id" => $transaction->getId(),
                            "ingredientId" => $transaction->getIngredientId(),
                            "type" => $transaction->getReason(),
                            "reason" => $transaction->getReason(),
                            "quantity" => $transaction->getQuantity()
                        ];
                }
            }
        }
    }
}
