<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Domain\Types\StockTransactionType;

class StockTransaction extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $primaryKey = "transaction_id";
    protected $keyType = 'string';

    protected $casts = [
        "type" => StockTransactionType::class,
    ];
}
