<?php

namespace Src\Domain\Entities;
class Merchant
{
    private int $id;
    private string $name;
    private string $email;

    /**
     * @var StockItem[];
     */
    private array $itemsToRefill = [];


    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
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

    public function notifyAboutStock(StockItem $stock) {
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
