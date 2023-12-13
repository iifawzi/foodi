<?php

namespace Src\Infrastructure\repositories\Eloquent;

use App\Models\IngredientStock;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Src\Application\ports\infrastructure\repositories\StockRepository;
use Src\Domain\Entities\StockItem;

class EloquentStockRepository implements StockRepository
{

    public function getStockItems(array $ingredientIds): array
    {
        $stockItems = IngredientStock::query()->whereIn("ingredient_id", $ingredientIds)->get()->all();

        $items = [];
        foreach ($stockItems as $stockItem) {
            $items[] = new StockItem(
                $stockItem["ingredient_id"],
                $stockItem["name"],
                $stockItem["full_quantity"],
                $stockItem["available_quantity"],
                $stockItem["min_threshold_percentage"]);
        }
        return $items;
    }

    public function updateStocks(array $stockItems): void
    {
        $ingredientIds = [];
        $caseStatements = [];
        $stockTransactions = [];

        foreach ($stockItems as $stockItem) {
            $ingredientIds[] = $stockItem->getId();
            $transactions = $stockItem->getStockTransactions();

            $caseStatements[] = "WHEN {$stockItem->getId()} THEN {$stockItem->getAvailableQuantity()}";

            if (count($transactions)) {
                foreach ($transactions as $stockTransaction) {
                    $stockTransactions[] = [
                            "transaction_id" => $stockTransaction->getId(),
                            "ingredient_id" => $stockTransaction->getIngredientId(),
                            "type" => $stockTransaction->getType(),
                            "reason" => $stockTransaction->getReason(),
                            "quantity" => $stockTransaction->getQuantity()
                    ];
                }
            }
        }
        $caseStatements = implode(" ", $caseStatements);
        // The goal is to update all the stocks in a single query.
        IngredientStock::query()
            ->whereIn('ingredient_id', $ingredientIds)
            ->update([
                'available_quantity' => DB::raw("CASE ingredient_id $caseStatements ELSE available_quantity END")
            ]);
        StockTransaction::factory()->createMany($stockTransactions);
    }
}
