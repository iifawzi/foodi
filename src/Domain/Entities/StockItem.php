<?php

namespace Src\Domain\Entities;
use Src\Domain\Types\StockItemStatus;

class StockItem
{
    private int $id;
    private int $fullQuantity;
    private int $availableQuantity;
    private float $thresholdPercentage;

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

    public function consume($quantity): StockItemStatus
    {
        if ($quantity > $this->availableQuantity) {
            return StockItemStatus::OUTSTOCK;
        }

        $this->availableQuantity -= $quantity;
        if ($this->availableQuantity <= $this->fullQuantity * $this->thresholdPercentage) {
            $this->stockStatus = StockItemStatus::LOWSTOCK;
        }
        return $this->stockStatus;
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

}
