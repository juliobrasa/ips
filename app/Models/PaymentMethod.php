<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_payment_method_id',
        'type',
        'card_brand',
        'card_last_four',
        'card_exp_month',
        'card_exp_year',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $hidden = [
        'stripe_payment_method_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'card' && $this->card_brand) {
            return ucfirst($this->card_brand) . ' ****' . $this->card_last_four;
        }
        return ucfirst($this->type);
    }

    public function getExpirationAttribute(): string
    {
        if ($this->card_exp_month && $this->card_exp_year) {
            return sprintf('%02d/%d', $this->card_exp_month, $this->card_exp_year % 100);
        }
        return '';
    }

    public function isExpired(): bool
    {
        if (!$this->card_exp_month || !$this->card_exp_year) {
            return false;
        }

        $expDate = \Carbon\Carbon::createFromDate($this->card_exp_year, $this->card_exp_month, 1)->endOfMonth();
        return $expDate->isPast();
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
