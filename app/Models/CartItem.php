<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subnet_id',
        'lease_months',
        'reserved_until',
    ];

    protected function casts(): array
    {
        return [
            'reserved_until' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subnet(): BelongsTo
    {
        return $this->belongsTo(Subnet::class);
    }

    public function isReserved(): bool
    {
        return $this->reserved_until && $this->reserved_until->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->reserved_until && $this->reserved_until->isPast();
    }

    public function reserve(int $minutes = 15): void
    {
        $this->update(['reserved_until' => now()->addMinutes($minutes)]);
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->subnet->total_monthly_price * $this->lease_months;
    }

    public function getMonthlyPriceAttribute(): float
    {
        return $this->subnet->total_monthly_price;
    }
}
