<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subnet' => new SubnetResource($this->whenLoaded('subnet')),
            'lessee_company' => $this->whenLoaded('lesseeCompany', function () {
                return [
                    'id' => $this->lesseeCompany->id,
                    'name' => $this->lesseeCompany->name,
                ];
            }),
            'holder_company' => $this->whenLoaded('subnet', function () {
                return $this->subnet->company ? [
                    'id' => $this->subnet->company->id,
                    'name' => $this->subnet->company->name,
                ] : null;
            }),
            'duration' => [
                'months' => $this->duration_months,
                'start_date' => $this->start_date->toIso8601String(),
                'end_date' => $this->end_date->toIso8601String(),
                'days_remaining' => max(0, now()->diffInDays($this->end_date, false)),
            ],
            'pricing' => [
                'monthly_price' => (float) $this->monthly_price,
                'total_value' => (float) ($this->monthly_price * $this->duration_months),
            ],
            'status' => $this->status,
            'asn' => $this->asn,
            'loa' => $this->whenLoaded('loa', function () {
                return $this->loa ? [
                    'id' => $this->loa->id,
                    'number' => $this->loa->loa_number,
                    'verification_code' => $this->loa->verification_code,
                ] : null;
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
