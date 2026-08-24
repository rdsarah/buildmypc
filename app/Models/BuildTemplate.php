<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BuildTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_use',
    ];

    /**
     * Get the user that owns the build template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products in the build template.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'build_template_products', 'build_template_id', 'product_id')
            ->withTimestamps();
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) $this->products->sum('price');
    }

    public function getTotalWattageAttribute(): int
    {
        return (int) $this->products->sum('wattage');
    }
}