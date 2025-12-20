<?php

namespace App\Services;

use App\Models\Subnet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RpkiService
{
    protected string $ripeStatBaseUrl = 'https://stat.ripe.net/data';

    /**
     * Get ROA status for a prefix
     */
    public function getRoaStatus(string $prefix): array
    {
        $cacheKey = "rpki:roa:{$prefix}";

        return Cache::remember($cacheKey, 3600, function () use ($prefix) {
            try {
                $response = Http::timeout(30)->get("{$this->ripeStatBaseUrl}/rpki-validation/data.json", [
                    'resource' => $prefix,
                    'sourceapp' => 'ips-marketplace',
                ]);

                if (!$response->successful()) {
                    return ['status' => 'unknown', 'error' => 'API request failed'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'status' => $data['status'] ?? 'unknown',
                    'validating_roas' => $data['validating_roas'] ?? [],
                    'description' => $this->getStatusDescription($data['status'] ?? 'unknown'),
                ];
            } catch (\Exception $e) {
                Log::error('RPKI validation failed', ['prefix' => $prefix, 'error' => $e->getMessage()]);
                return ['status' => 'error', 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Get all ROAs for an ASN
     */
    public function getRoasForAsn(int $asn): array
    {
        $cacheKey = "rpki:asn:{$asn}";

        return Cache::remember($cacheKey, 3600, function () use ($asn) {
            try {
                $response = Http::timeout(30)->get("{$this->ripeStatBaseUrl}/rpki-roas/data.json", [
                    'resource' => "AS{$asn}",
                    'sourceapp' => 'ips-marketplace',
                ]);

                if (!$response->successful()) {
                    return ['roas' => [], 'error' => 'API request failed'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'roas' => $data['roas'] ?? [],
                    'total' => count($data['roas'] ?? []),
                ];
            } catch (\Exception $e) {
                Log::error('RPKI ROAs fetch failed', ['asn' => $asn, 'error' => $e->getMessage()]);
                return ['roas' => [], 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Check if a prefix-origin pair is valid
     */
    public function validatePrefixOrigin(string $prefix, int $asn): array
    {
        $cacheKey = "rpki:validate:{$prefix}:{$asn}";

        return Cache::remember($cacheKey, 1800, function () use ($prefix, $asn) {
            try {
                $response = Http::timeout(30)->get("{$this->ripeStatBaseUrl}/rpki-validation/data.json", [
                    'resource' => $prefix,
                    'origin' => "AS{$asn}",
                    'sourceapp' => 'ips-marketplace',
                ]);

                if (!$response->successful()) {
                    return ['valid' => null, 'status' => 'unknown'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'valid' => $data['status'] === 'valid',
                    'status' => $data['status'] ?? 'unknown',
                    'validating_roas' => $data['validating_roas'] ?? [],
                ];
            } catch (\Exception $e) {
                return ['valid' => null, 'status' => 'error', 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Get RPKI history for a prefix
     */
    public function getRpkiHistory(string $prefix, ?string $startTime = null): array
    {
        try {
            $params = [
                'resource' => $prefix,
                'sourceapp' => 'ips-marketplace',
            ];

            if ($startTime) {
                $params['starttime'] = $startTime;
            }

            $response = Http::timeout(30)->get("{$this->ripeStatBaseUrl}/rpki-history/data.json", $params);

            if (!$response->successful()) {
                return ['history' => [], 'error' => 'API request failed'];
            }

            $data = $response->json()['data'] ?? [];

            return [
                'history' => $data['history'] ?? [],
                'resource' => $prefix,
            ];
        } catch (\Exception $e) {
            return ['history' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Get suggested ROA for a prefix based on BGP observations
     */
    public function getSuggestedRoa(string $prefix): array
    {
        try {
            // Get BGP origin AS
            $response = Http::timeout(30)->get("{$this->ripeStatBaseUrl}/prefix-overview/data.json", [
                'resource' => $prefix,
                'sourceapp' => 'ips-marketplace',
            ]);

            if (!$response->successful()) {
                return ['suggestion' => null];
            }

            $data = $response->json()['data'] ?? [];
            $asns = $data['asns'] ?? [];

            if (empty($asns)) {
                return ['suggestion' => null, 'message' => 'No BGP origin found'];
            }

            // Get the most common origin
            $originAsn = $asns[0]['asn'] ?? null;

            if (!$originAsn) {
                return ['suggestion' => null];
            }

            // Parse prefix to get max length
            $parts = explode('/', $prefix);
            $prefixLength = (int) ($parts[1] ?? 24);

            return [
                'suggestion' => [
                    'prefix' => $prefix,
                    'origin_asn' => $originAsn,
                    'max_length' => min($prefixLength + 4, str_contains($prefix, ':') ? 48 : 24),
                ],
                'observed_origins' => $asns,
            ];
        } catch (\Exception $e) {
            return ['suggestion' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Batch validate multiple prefixes
     */
    public function batchValidate(array $prefixes): array
    {
        $results = [];

        foreach ($prefixes as $prefix) {
            $results[$prefix] = $this->getRoaStatus($prefix);
        }

        return $results;
    }

    /**
     * Get RPKI statistics summary for user's subnets
     */
    public function getUserRpkiSummary(int $userId): array
    {
        $subnets = Subnet::where('user_id', $userId)->pluck('cidr')->toArray();

        if (empty($subnets)) {
            return [
                'total' => 0,
                'valid' => 0,
                'invalid' => 0,
                'not_found' => 0,
                'unknown' => 0,
            ];
        }

        $results = $this->batchValidate($subnets);

        $summary = [
            'total' => count($subnets),
            'valid' => 0,
            'invalid' => 0,
            'not_found' => 0,
            'unknown' => 0,
        ];

        foreach ($results as $result) {
            $status = $result['status'] ?? 'unknown';
            match ($status) {
                'valid' => $summary['valid']++,
                'invalid' => $summary['invalid']++,
                'not-found' => $summary['not_found']++,
                default => $summary['unknown']++,
            };
        }

        return $summary;
    }

    /**
     * Get status description
     */
    protected function getStatusDescription(string $status): string
    {
        return match ($status) {
            'valid' => 'The prefix has a valid ROA and the announcement is covered',
            'invalid' => 'The prefix has a ROA but the announcement is not covered (wrong origin or prefix length)',
            'not-found' => 'No ROA exists for this prefix',
            default => 'Unable to determine RPKI status',
        };
    }

    /**
     * Check if subnet needs ROA creation
     */
    public function needsRoa(Subnet $subnet): bool
    {
        $status = $this->getRoaStatus($subnet->cidr);
        return in_array($status['status'] ?? '', ['not-found', 'unknown']);
    }
}
