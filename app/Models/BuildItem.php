<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildItem extends Model
{
    protected $fillable = [
        'build_id',
        'category_id',
        'product_id',
    ];

    public function build()
    {
        return $this->belongsTo(Build::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}