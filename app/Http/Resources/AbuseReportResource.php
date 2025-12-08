<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbuseReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subnet' => new SubnetResource($this->whenLoaded('subnet')),
            'lease' => $this->whenLoaded('lease', function () {
                return $this->lease ? [
                    'id' => $this->lease->id,
                    'lessee' => $this->lease->lesseeCompany->name ?? null,
                ] : null;
            }),
            'type' => $this->type,
            'severity' => $this->severity,
            'source' => $this->source,
            'description' => $this->description,
            'evidence' => $this->evidence,
            'status' => $this->status,
            'resolution' => [
                'notes' => $this->resolution_notes,
                'resolved_at' => $this->resolved_at?->toIso8601String(),
                'resolved_by' => $this->whenLoaded('resolvedByUser', function () {
                    return $this->resolvedByUser ? [
                        'id' => $this->resolvedByUser->id,
                        'name' => $this->resolvedByUser->name,
                    ] : null;
                }),
            ],
            'severity_color' => $this->severity_color,
            'status_color' => $this->status_color,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
