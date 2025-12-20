<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipeCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'api_key',
        'maintainer',
        'allowed_object_types',
        'is_active',
        'validated_at',
        'expires_at',
    ];

    protected $casts = [
        'allowed_object_types' => 'array',
        'is_active' => 'boolean',
        'validated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key',
    ];

    /**
     * Get the company that owns the credential
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Encrypt the API key before storing
     */
    public function setApiKeyAttribute(string $value): void
    {
        $this->attributes['api_key'] = encrypt($value);
    }

    /**
     * Decrypt the API key when accessing
     */
    public function getApiKeyAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the decrypted API key
     */
    public function getDecryptedApiKey(): ?string
    {
        return $this->api_key;
    }

    /**
     * Check if credential is valid and not expired
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if credential can manage a specific object type
     */
    public function canManage(string $objectType): bool
    {
        if (!$this->allowed_object_types) {
            return true; // No restrictions
        }

        return in_array($objectType, $this->allowed_object_types);
    }

    /**
     * Mark credential as validated
     */
    public function markAsValidated(): void
    {
        $this->update(['validated_at' => now()]);
    }

    /**
     * Scope for active credentials
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for credentials expiring soon
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }
}
