<?php

namespace Src\Domain\Entities;

use Ramsey\Uuid\UuidInterface;
use Illuminate\Support\Str;
use Src\Domain\Types\OrderStatus;

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
    private array $ingredients = [];

    public function __construct()
    {
        $this->id = Str::uuid();
        $this->status = OrderStatus::PENDING;
    }

    /**
     * @param  Item[] $items
     * @return void
     */

    public function addItems(array $items): void
    {
        foreach ($items as $item) {
            $this->items[] = $item;
            foreach ($item->getIngredients() as $ingredient) {
                $ingredientId = $ingredient->getId();
                $this->ingredients[$ingredientId] = ($this->ingredients[$ingredientId] ?? 0) +
                    $ingredient->getQuantity() * $item->getQuantity();
            }
        }
    }


    public
    function getId(): UuidInterface
    {
        return $this->id;
    }

    public
    function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return Item[]
     */
    public
    function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, int>
     */
    public
    function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }
}
