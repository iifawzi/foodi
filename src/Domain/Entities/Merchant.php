<?php

namespace Src\Domain\Entities;
class Merchant
{

    /**
     * @var StockItem[];
     */
    private array $itemsToRefill = [];


    public function __construct(
        private readonly int    $id,
        private readonly string $name,
        private readonly string $email
    )
    {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function notifyAboutStock(StockItem $stock): void
    {
        $this->itemsToRefill[] = $stock;
    }

    /**
     * @return StockItem[]
     */
    public function getItemsToRefill(): array
    {
        return $this->itemsToRefill;
    }
}
