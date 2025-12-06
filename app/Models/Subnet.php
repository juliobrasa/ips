<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Subnet extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'ip_address',
        'cidr',
        'rir',
        'geolocation_country',
        'geolocation_city',
        'price_per_ip_monthly',
        'min_lease_months',
        'status',
        'verification_token',
        'ownership_verified_at',
        'rpki_delegated',
        'auto_renewal',
        'reputation_score',
        'last_reputation_check',
        'blocklist_results',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'ownership_verified_at' => 'datetime',
            'last_reputation_check' => 'datetime',
            'blocklist_results' => 'array',
            'rpki_delegated' => 'boolean',
            'auto_renewal' => 'boolean',
            'price_per_ip_monthly' => 'decimal:4',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease(): ?Lease
    {
        return $this->leases()->where('status', 'active')->first();
    }

    public function abuseReports(): HasMany
    {
        return $this->hasMany(AbuseReport::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('ownership_verified_at');
    }

    public function scopeClean(Builder $query): Builder
    {
        return $query->where('reputation_score', '>=', 80);
    }

    public function scopeByRir(Builder $query, string $rir): Builder
    {
        return $query->where('rir', $rir);
    }

    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->where('geolocation_country', $country);
    }

    public function scopeByCidr(Builder $query, int $cidr): Builder
    {
        return $query->where('cidr', $cidr);
    }

    public function scopePriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('price_per_ip_monthly', [$min, $max]);
    }

    // Helpers
    public function getIpCountAttribute(): int
    {
        return pow(2, 32 - $this->cidr);
    }

    public function getTotalMonthlyPriceAttribute(): float
    {
        return $this->price_per_ip_monthly * $this->ip_count;
    }

    public function getCidrNotationAttribute(): string
    {
        return "{$this->ip_address}/{$this->cidr}";
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isLeased(): bool
    {
        return $this->status === 'leased';
    }

    public function hasCleanReputation(): bool
    {
        return $this->reputation_score >= 80;
    }
}
