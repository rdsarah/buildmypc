<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'total_price',
        'estimated_wattage',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'estimated_wattage' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(BuildItem::class);
    }
}