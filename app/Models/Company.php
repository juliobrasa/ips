<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'legal_name',
        'tax_id',
        'country',
        'address',
        'city',
        'postal_code',
        'company_type',
        'entity_type',
        'identity_document_type',
        'identity_document_number',
        'identity_document_file',
        'identity_document_uploaded_at',
        'kyc_signed_document',
        'kyc_signed_uploaded_at',
        'legal_representative_name',
        'legal_representative_id',
        'legal_representative_position',
        'kyc_status',
        'kyc_documents',
        'kyc_approved_at',
        'kyc_notes',
        'kyc_reviewed_at',
        'kyc_reviewed_by',
        'payout_method',
        'payout_details',
    ];

    protected function casts(): array
    {
        return [
            'kyc_documents' => 'array',
            'payout_details' => 'array',
            'kyc_approved_at' => 'datetime',
            'kyc_reviewed_at' => 'datetime',
            'identity_document_uploaded_at' => 'datetime',
            'kyc_signed_uploaded_at' => 'datetime',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kyc_reviewed_by');
    }

    public function isIndividual(): bool
    {
        return $this->entity_type === 'individual';
    }

    public function isCompanyEntity(): bool
    {
        return $this->entity_type === 'company';
    }

    public function hasIdentityDocument(): bool
    {
        return !empty($this->identity_document_file);
    }

    public function hasSignedKyc(): bool
    {
        return !empty($this->kyc_signed_document);
    }

    public function hasAllDocuments(): bool
    {
        return $this->hasIdentityDocument() && $this->hasSignedKyc();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subnets(): HasMany
    {
        return $this->hasMany(Subnet::class);
    }

    public function leasesAsHolder(): HasMany
    {
        return $this->hasMany(Lease::class, 'holder_company_id');
    }

    public function leasesAsLessee(): HasMany
    {
        return $this->hasMany(Lease::class, 'lessee_company_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function isHolder(): bool
    {
        return in_array($this->company_type, ['holder', 'both']);
    }

    public function isLessee(): bool
    {
        return in_array($this->company_type, ['lessee', 'both']);
    }

    public function isKycApproved(): bool
    {
        return $this->kyc_status === 'approved';
    }

    public function canList(): bool
    {
        return $this->isHolder() && $this->isKycApproved();
    }

    public function canLease(): bool
    {
        return $this->isLessee() && $this->isKycApproved();
    }
}
