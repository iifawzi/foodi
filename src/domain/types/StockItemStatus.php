<?php

namespace types;

enum StockItemStatus:string
{
    case INSTOCK = "INSTOCK";
    case LOWSTOCK = "LOWSTOCK";

    case OUTSTOCK = "OUTSTOCK";
}
