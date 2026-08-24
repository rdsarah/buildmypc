<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'cancellation_reason',
        'cancelled_by',
        'total_amount',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'bg-warning text-dark',
            'processing' => 'bg-info text-dark',
            'shipped'    => 'bg-primary',
            'delivered'  => 'bg-success',
            'cancelled'  => 'bg-danger',
            default      => 'bg-secondary',
        };
    }
}