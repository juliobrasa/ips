<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubnetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'cidr' => $this->cidr,
            'cidr_notation' => $this->cidr_notation,
            'rir' => $this->rir,
            'geolocation' => [
                'country' => $this->geolocation_country,
                'city' => $this->geolocation_city,
            ],
            'pricing' => [
                'price_per_ip_monthly' => (float) $this->price_per_ip_monthly,
                'total_monthly_price' => (float) $this->total_monthly_price,
                'ip_count' => $this->ip_count,
                'min_lease_months' => $this->min_lease_months,
            ],
            'status' => $this->status,
            'verification' => [
                'ownership_verified' => (bool) $this->ownership_verified_at,
                'ownership_verified_at' => $this->ownership_verified_at?->toIso8601String(),
            ],
            'reputation' => [
                'score' => $this->reputation_score,
                'last_check' => $this->last_reputation_check?->toIso8601String(),
                'is_clean' => $this->hasCleanReputation(),
                'blocklist_count' => is_array($this->blocklist_results)
                    ? count(array_filter($this->blocklist_results, fn($r) => $r['listed'] ?? false))
                    : 0,
            ],
            'features' => [
                'rpki_delegated' => (bool) $this->rpki_delegated,
                'auto_renewal' => (bool) $this->auto_renewal,
            ],
            'description' => $this->description,
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
