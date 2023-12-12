<?php

namespace Src\Domain\Entities;
class Item
{
    private readonly int $totalPrice;

    /**
     * @var array<int, Ingredient>
     */
    private array $ingredients;

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly int $unitPrice,
        private readonly string $quantity)
    {
        $this->totalPrice = $this->unitPrice * $this->quantity;
    }

    public function setIngredient(Ingredient $ingredient): void
    {
        $this->ingredients[$ingredient->getId()] = $ingredient;
    }

    /**
     * @returns array<int, Ingredient>
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }
}
