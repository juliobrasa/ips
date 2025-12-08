<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReputationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resource;

        return [
            'ip' => $data['ip'] ?? null,
            'score' => $data['score'] ?? $data['overall_score'] ?? null,
            'status' => $data['status'] ?? $data['overall_status'] ?? null,
            'can_list' => $data['can_list'] ?? $data['can_be_listed'] ?? false,
            'blocklists' => [
                'count' => $data['blocked_count'] ?? $data['blocklist_count'] ?? 0,
                'total_checked' => $data['total_checked'] ?? 11,
                'details' => $this->formatBlocklistDetails($data),
            ],
            'abuseipdb' => isset($data['abuseipdb']) ? [
                'confidence_score' => $data['abuseipdb']['abuse_confidence_score'] ?? null,
                'total_reports' => $data['abuseipdb']['total_reports'] ?? 0,
                'last_reported' => $data['abuseipdb']['last_reported_at'] ?? null,
                'isp' => $data['abuseipdb']['isp'] ?? null,
                'usage_type' => $data['abuseipdb']['usage_type'] ?? null,
            ] : null,
            'recommendation' => $data['recommendation'] ?? null,
            'checked_at' => $data['checked_at'] ?? now()->toIso8601String(),
        ];
    }

    protected function formatBlocklistDetails(array $data): array
    {
        $details = $data['blocklist_details'] ?? $data['blocklists'] ?? [];

        if (empty($details)) {
            return [];
        }

        // If it's already formatted as array of objects
        if (isset($details[0]['blocklist'])) {
            return $details;
        }

        // Convert from key-value format
        $formatted = [];
        foreach ($details as $blocklist => $info) {
            if (is_array($info) && ($info['listed'] ?? false)) {
                $formatted[] = [
                    'blocklist' => $blocklist,
                    'listed' => true,
                    'weight' => $info['weight'] ?? 0,
                    'severity' => $this->getSeverity($blocklist),
                    'delisting_url' => $this->getDelistingUrl($blocklist),
                ];
            }
        }

        return $formatted;
    }

    protected function getSeverity(string $blocklist): string
    {
        $critical = ['zen.spamhaus.org', 'b.barracudacentral.org', 'cbl.abuseat.org'];
        $high = ['bl.spamcop.net', 'dnsbl.sorbs.net', 'dnsbl-1.uceprotect.net'];

        if (in_array($blocklist, $critical)) return 'critical';
        if (in_array($blocklist, $high)) return 'high';
        return 'medium';
    }

    protected function getDelistingUrl(string $blocklist): ?string
    {
        $urls = [
            'zen.spamhaus.org' => 'https://www.spamhaus.org/lookup/',
            'b.barracudacentral.org' => 'https://www.barracudacentral.org/lookups',
            'bl.spamcop.net' => 'https://www.spamcop.net/bl.shtml',
            'dnsbl.sorbs.net' => 'http://www.sorbs.net/lookup.shtml',
            'cbl.abuseat.org' => 'https://www.abuseat.org/lookup.cgi',
        ];

        return $urls[$blocklist] ?? null;
    }
}
