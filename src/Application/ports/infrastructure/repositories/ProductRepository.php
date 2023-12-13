<?php

namespace Src\Application\ports\infrastructure\repositories;
use Illuminate\Support\Collection;
use Src\Domain\Entities\Item;

interface ProductRepository
{
    /**
     * @param  Collection<int, int> $productQuantities
     * @return Item[]
     */
    public function getItems(Collection $productQuantities): array;
}
