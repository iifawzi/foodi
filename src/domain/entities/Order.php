<?php

use Ramsey\Uuid\UuidInterface;
use Illuminate\Support\Str;
use types\OrderStatus;
use types\StockItemStatus;

class Order
{
    private UuidInterface $id;
    private OrderStatus $status;

    /**
     * @var Item[]
     */
    private array $items;

    /**
     * @var array<int, int>
     */
    private array $ingredients;

    public function __construct()
    {
        $this->id = Str::uuid();
        $this->status = OrderStatus::PENDING;
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
        foreach ($item->getIngredients() as $ingredient) {
            $ingredientId = $ingredient->getId();
            $this->ingredients[$ingredientId] = ($this->ingredients[$ingredientId] ?? 0) +
                $ingredient->getQuantity() * $item->getQuantity();
        }

    }

    /**
     * @param StockItem[] $stock
     * @return bool
     */
    public function confirmOrder(array $stock): bool
    {
        $status = $this->checkStock($stock);
        if (!$status) {
            $this->status = OrderStatus::CANCELLED;
            return false;
        }
        $this->status = OrderStatus::CREATED;
        return true;
    }

    /**
     * @param StockItem[] $stockItems
     */
    private function checkStock(array $stockItems): bool
    {
        foreach ($stockItems as $stock) {
            $stockStatus = $stock->consume($this->ingredients[$stock->getId()]);
            if ($stockStatus == StockItemStatus::LOWSTOCK) {
                return false;
            }
        }
        return true;
    }


    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @returns Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, int>
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
