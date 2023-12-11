<?php

namespace Src\Domain\Types;

enum StockItemStatus: string
{
    case INSTOCK = "INSTOCK";
    case LOWSTOCK = "LOWSTOCK";
    case INSUFFICIENT = "INSUFFICIENT";
}
