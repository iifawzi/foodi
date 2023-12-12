<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IngredientStock extends Model
{
    use HasFactory;
    protected $primaryKey = "ingredient_id";

    public function stockTransactions(): void {
        $this->hasMany(
            StockTransaction::class,
            'ingredient_id',
            'ingredient_id');
    }
}
