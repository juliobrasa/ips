<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'gross_amount',
        'platform_fee',
        'net_amount',
        'currency',
        'payout_method',
        'payout_details',
        'status',
        'transaction_reference',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'payout_details' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted(string $reference = null): void
    {
        $this->update([
            'status' => 'completed',
            'transaction_reference' => $reference,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(string $notes = null): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $notes,
        ]);
    }
}
