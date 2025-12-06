<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'subnet_id',
        'lessee_company_id',
        'holder_company_id',
        'start_date',
        'end_date',
        'auto_renew',
        'monthly_price',
        'platform_fee_percentage',
        'status',
        'assigned_asn',
        'loa_generated_at',
        'roa_created_at',
        'termination_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'auto_renew' => 'boolean',
            'monthly_price' => 'decimal:2',
            'platform_fee_percentage' => 'decimal:2',
            'loa_generated_at' => 'datetime',
            'roa_created_at' => 'datetime',
        ];
    }

    public function subnet(): BelongsTo
    {
        return $this->belongsTo(Subnet::class);
    }

    public function lesseeCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'lessee_company_id');
    }

    public function holderCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'holder_company_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function loa(): HasOne
    {
        return $this->hasOne(Loa::class);
    }

    public function abuseReports(): HasMany
    {
        return $this->hasMany(AbuseReport::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring(Builder $query, int $days = 30): Builder
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                     ->where('status', 'active');
    }

    public function scopeForLessee(Builder $query, int $companyId): Builder
    {
        return $query->where('lessee_company_id', $companyId);
    }

    public function scopeForHolder(Builder $query, int $companyId): Builder
    {
        return $query->where('holder_company_id', $companyId);
    }

    // Helpers
    public function getPlatformFeeAttribute(): float
    {
        return $this->monthly_price * ($this->platform_fee_percentage / 100);
    }

    public function getHolderEarningsAttribute(): float
    {
        return $this->monthly_price - $this->platform_fee;
    }

    public function getDurationMonthsAttribute(): int
    {
        return $this->start_date->diffInMonths($this->end_date);
    }

    public function getRemainingDaysAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function canRenew(): bool
    {
        return $this->isActive() && $this->auto_renew;
    }

    public function needsAsn(): bool
    {
        return $this->status === 'pending_assignment' && empty($this->assigned_asn);
    }
}
