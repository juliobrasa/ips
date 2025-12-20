<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WebhookEndpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'secret',
        'events',
        'is_active',
        'failure_count',
        'last_response_code',
        'last_triggered_at',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    protected $hidden = [
        'secret',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($webhook) {
            if (!$webhook->secret) {
                $webhook->secret = Str::random(32);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isHealthy(): bool
    {
        return $this->failure_count < 5 && $this->is_active;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHealthy($query)
    {
        return $query->where('is_active', true)->where('failure_count', '<', 5);
    }
}
