<?php

namespace types;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CREATED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case PROCESSING = 'PROCESSING';
    case FULFILLED = 'FULFILLED';
}

;
