<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'subject',
        'category',
        'priority',
        'status',
        'related_lease_id',
        'related_subnet_id',
        'first_response_at',
        'resolved_at',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'billing' => 'Billing & Payments',
        'technical' => 'Technical Support',
        'abuse' => 'Abuse Report',
        'sales' => 'Sales Inquiry',
        'kyc' => 'KYC/Verification',
        'other' => 'Other',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'waiting_customer' => 'Waiting for Customer',
        'waiting_internal' => 'Waiting Internal',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    public function relatedLease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'related_lease_id');
    }

    public function relatedSubnet(): BelongsTo
    {
        return $this->belongsTo(Subnet::class, 'related_subnet_id');
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function isOpen(): bool
    {
        return !in_array($this->status, ['resolved', 'closed']);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'primary',
            'in_progress' => 'info',
            'waiting_customer' => 'warning',
            'waiting_internal' => 'secondary',
            'resolved' => 'success',
            'closed' => 'dark',
            default => 'secondary',
        };
    }
}
