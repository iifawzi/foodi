<?php

namespace Src\Domain\Entities;

use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;
use Src\Domain\Types\StockTransactionType;

class StockTransaction
{
    private UuidInterface $id;
    private int $ingredientId;
    private int $quantity;
    private string $reason;
    private StockTransactionType $type = StockTransactionType::OUT;

    public function __construct(int $ingredientId, int $quantity, string $reason)
    {
        $this->id = Str::uuid();
        $this->quantity = $quantity;
        $this->reason = $reason;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
