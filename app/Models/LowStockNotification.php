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
    protected $primaryKey = "notification_id";
    protected $keyType = "string";
    protected $casts = [
        'status' => LowStockNotificationType::class
    ];

    public function ingredientStock(): BelongsTo
    {
        return $this->belongsTo(
            IngredientStock::class, 'ingredient_id', 'ingredient_id');
    }
}
