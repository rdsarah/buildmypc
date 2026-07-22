<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'model',
        'price',
        'stock',
        'image_url',
        'description',
        'socket_type',
        'ram_type',
        'form_factor',
        'wattage',
        'storage_type',
        'gpu_length_mm',
        'case_clearance_mm',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Tk ' . number_format($this->price, 0);
    }

    public function getStockStatusAttribute(): string
    {
        return $this->stock > 0 ? 'In Stock' : 'Out of Stock';
    }
}