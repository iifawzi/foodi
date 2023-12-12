<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Domain\Types\OrderStatus;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = "order_id";
    protected $keyType = 'string';

    protected $casts = [
        'status' => OrderStatus::class
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
}
