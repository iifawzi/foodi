<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Application\ports\infrastructure\repositories\MerchantRepository;
use Src\Infrastructure\types\LowStockNotificationType;

class LowStockNotification extends Model
{
    use HasFactory;
    protected $casts = [
        'status' => LowStockNotificationType::class
    ];
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(
            MerchantRepository::class, 'merchant_id', 'merchant_id');
    }

    public function ingredientStock(): BelongsTo
    {
        return $this->belongsTo(
            IngredientStock::class, 'ingredient_id', 'ingredient_id');
    }
}
