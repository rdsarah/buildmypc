<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BuildTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'target_use',
        'target_use',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'build_template_products', 'build_template_id', 'product_id')
            ->withTimestamps();
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->products->sum('price');
    }

    public function getTotalWattageAttribute(): int
    {
        return $this->products->sum('wattage');
    }
}
