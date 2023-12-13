<?php

namespace Src\Domain\Entities;
class Ingredient
{

    public function __construct(
        private readonly int $id,
        private readonly int $quantity
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
