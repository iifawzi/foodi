<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IngredientStock extends Model
{
    use HasFactory;
    protected $primaryKey = "ingredient_id";

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(
            Merchant::class, 'merchant_id', 'merchant_id'
        );
    }
}
