<?php

namespace Src\Application\ports\infrastructure;
use Illuminate\Support\Collection;
use Src\Domain\Entities\Item;

interface ProductRepository
{
    /**
     * @param Collection $productQuantities
     * @return Item[]
     */
    public function getItems(Collection $productQuantities): array;
}
