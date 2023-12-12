<?php

namespace Src\Domain\Entities;

use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;
use Src\Domain\Types\StockTransactionType;

class StockTransaction
{
    private UuidInterface $id;
    private StockTransactionType $type = StockTransactionType::OUT;

    public function __construct(
        private readonly int $ingredientId,
        private readonly int $quantity,
        private readonly string $reason
    )
    {
        $this->id = Str::uuid();
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

    /**
     * @return StockTransactionType
     */
    public function getType(): StockTransactionType
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getIngredientId(): int
    {
        return $this->ingredientId;
    }
}
