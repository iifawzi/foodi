<?php

namespace Src\Domain\Entities;

use Ramsey\Uuid\UuidInterface;
use Src\Domain\Types\StockItemStatus;

class StockItem
{
    private int $thresholdLimit;

    /**
     * @var StockTransaction[]
     */
    private array $stockTransactions = [];
    private StockItemStatus $stockStatus = StockItemStatus::INSTOCK;

    public function __construct(
        private readonly int $id,
        private readonly int $merchantId,
        private readonly string $name,
        private readonly int $fullQuantity,
        private int $availableQuantity,
        readonly float $thresholdPercentage)
    {
        $this->thresholdLimit = ($thresholdPercentage / 100) * $fullQuantity;

        if ($availableQuantity <= $this->thresholdLimit) {
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
        if ($this->availableQuantity < $this->thresholdLimit) {
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getThresholdLimit(): int
    {
        return $this->thresholdLimit;
    }

    public function getMerchantId(): int
    {
        return $this->merchantId;
    }
}
