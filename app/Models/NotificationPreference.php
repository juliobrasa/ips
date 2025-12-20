<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'database_enabled',
        'webhook_enabled',
        'sms_enabled',
        'telegram_enabled',
        'slack_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'database_enabled' => 'boolean',
        'webhook_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'telegram_enabled' => 'boolean',
        'slack_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
