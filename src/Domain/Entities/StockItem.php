<?php

namespace Src\Domain\Entities;

use Ramsey\Uuid\UuidInterface;
use Src\Domain\Types\StockItemStatus;

class StockItem
{
    private int $id;
    private int $fullQuantity;
    private int $availableQuantity;
    private float $thresholdPercentage;

    /**
     * @var StockTransaction[]
     */
    private array $stockTransactions;
    private StockItemStatus $stockStatus = StockItemStatus::INSTOCK;

    public function __construct(int $id, int $fullQuantity, int $availableQuantity, float $thresholdPercentage)
    {
        $this->id = $id;
        $this->fullQuantity = $fullQuantity;
        $this->availableQuantity = $availableQuantity;
        $this->thresholdPercentage = $thresholdPercentage / 100;

        if ($availableQuantity <= $fullQuantity * $this->thresholdPercentage) {
            $this->stockStatus = StockItemStatus::LOWSTOCK;
        }
    }

    public function consume(int $quantity, UuidInterface $orderId): StockItemStatus
    {
        if ($quantity > $this->availableQuantity) {
            return StockItemStatus::INSUFFICIENT;
        }

        $this->availableQuantity -= $quantity;
        $this->stockTransactions[] = new StockTransaction($this->id, $quantity, 'order: ' . $orderId);
        if ($this->availableQuantity <= $this->fullQuantity * $this->thresholdPercentage) {
            if ($this->stockStatus != StockItemStatus::LOWSTOCK) {
                $this->stockStatus = StockItemStatus::LOWSTOCK;
            }

        }
        return $this->stockStatus;
    }

    public function canConsume($quantity): bool
    {
        if ($quantity > $this->availableQuantity) {
            return false;
        }
        return true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFullQuantity(): int
    {
        return $this->fullQuantity;
    }

    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }

    /**
     * @return StockItemStatus
     */
    public function getStockStatus(): StockItemStatus
    {
        return $this->stockStatus;
    }

    /**
     * @return StockTransaction[]
     */
    public function getStockTransactions(): array
    {
        return $this->stockTransactions;
    }

}
