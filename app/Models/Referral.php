<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'commission_rate',
        'total_earnings',
        'total_orders',
        'activated_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'activated_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'active' => 'Active',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
