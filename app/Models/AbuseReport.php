<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class AbuseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'subnet_id',
        'lease_id',
        'type',
        'severity',
        'source',
        'description',
        'evidence',
        'status',
        'resolution_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected function casts(): array
    {
        return [
            'evidence' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships
    public function subnet(): BelongsTo
    {
        return $this->belongsTo(Subnet::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeInvestigating(Builder $query): Builder
    {
        return $query->where('status', 'investigating');
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', 'resolved');
    }

    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('severity', 'critical');
    }

    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->whereIn('severity', ['critical', 'high']);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'red',
            'investigating' => 'yellow',
            'resolved' => 'green',
            'dismissed' => 'gray',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'spam' => 'envelope',
            'phishing' => 'fish',
            'malware' => 'bug',
            'ddos' => 'bolt',
            'scraping' => 'spider',
            'fraud' => 'mask',
            default => 'exclamation-triangle',
        };
    }

    public function resolve(int $userId, string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $userId,
            'resolution_notes' => $notes,
        ]);
    }

    public function dismiss(int $userId, string $notes = null): void
    {
        $this->update([
            'status' => 'dismissed',
            'resolved_at' => now(),
            'resolved_by' => $userId,
            'resolution_notes' => $notes,
        ]);
    }
}
