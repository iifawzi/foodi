<?php

namespace Src\Infrastructure\types;

Enum LowStockNotificationType:string
{
    case PENDING = "PENDING";
    case FULFILLED = "FULFILLED";
}
