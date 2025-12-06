<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loa extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'loa_number',
        'ip_range',
        'authorized_asn',
        'valid_from',
        'valid_until',
        'holder_company_name',
        'lessee_company_name',
        'file_path',
        'signature_hash',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($loa) {
            if (empty($loa->loa_number)) {
                $loa->loa_number = self::generateLoaNumber();
            }
        });
    }

    public static function generateLoaNumber(): string
    {
        $prefix = 'LOA-' . date('Ym') . '-';
        $lastLoa = self::where('loa_number', 'like', $prefix . '%')
                      ->orderBy('loa_number', 'desc')
                      ->first();

        if ($lastLoa) {
            $lastNumber = (int) substr($lastLoa->loa_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->valid_until->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->valid_until->isPast();
    }

    public function revoke(): void
    {
        $this->update(['status' => 'revoked']);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('loa.download', $this);
    }
}
