<?php

namespace Src\Domain\Types;

enum StockTransactionType: string
{
    case OUT = "OUT";
    case IN = "IN";
}
