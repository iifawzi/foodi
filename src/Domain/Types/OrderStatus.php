<?php

namespace Src\Domain\Types;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case PROCESSING = 'PROCESSING';
    case FULFILLED = 'FULFILLED';
}

;
