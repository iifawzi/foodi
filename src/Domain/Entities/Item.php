<?php

namespace Src\Domain\Entities;
class Item
{
    private int $id;
    private string $name;
    private int $unitPrice;
    private string $quantity;

    /**
     * @var array<int, Ingredient>
     */
    private array $ingredients;

    public function __construct(int $id, string $name, int $price, string $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->unitPrice = $price;
        $this->quantity = $quantity;
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
}
