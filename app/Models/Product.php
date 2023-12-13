<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = "product_id";
    public $timestamps = false;
    public function ingredientStocks(): BelongsToMany
    {
        return $this->belongsToMany(
            IngredientStock::class,
            'product_ingredients',
            'product_id',
            'ingredient_id'
        )->withPivot('base_quantity');
    }
}
