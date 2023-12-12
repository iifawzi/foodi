<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory;

    protected $primaryKey = "merchant_id";
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'merchant_id', 'merchant_id');
    }

    public function ingredientsStock(): HasMany
    {
        return $this->hasMany(IngredientStock::class, 'merchant_id', 'merchant_id');
    }

    public function lowStockNotifications(): HasMany
    {
        return $this->hasMany(LowStockNotification::class, 'merchant_id', 'merchant_id');
    }
}
