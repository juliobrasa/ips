<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlacklistDelistingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'subnet_id',
        'blocklist',
        'status',
        'contact_email',
        'reason',
        'request_url',
        'response_message',
        'requested_by',
        'requested_at',
        'last_checked_at',
        'delisted_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'last_checked_at' => 'datetime',
            'delisted_at' => 'datetime',
        ];
    }

    public function subnet(): BelongsTo
    {
        return $this->belongsTo(Subnet::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['delisted', 'failed', 'manual_required']);
    }

    public function markAsDelisted(): void
    {
        $this->update([
            'status' => 'delisted',
            'delisted_at' => now(),
        ]);
    }

    public function markAsFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'response_message' => $message,
            'last_checked_at' => now(),
        ]);
    }
}
