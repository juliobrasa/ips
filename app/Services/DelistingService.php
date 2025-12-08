<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DelistingService
{
    /**
     * Blocklist information with delisting details
     */
    protected array $blocklists = [
        'zen.spamhaus.org' => [
            'name' => 'Spamhaus',
            'delisting_url' => 'https://www.spamhaus.org/lookup/',
            'auto_delist' => false,
            'delisting_method' => 'manual',
            'instructions' => 'Visit the lookup page, enter your IP, and follow the removal process.',
        ],
        'b.barracudacentral.org' => [
            'name' => 'Barracuda',
            'delisting_url' => 'https://www.barracudacentral.org/lookups',
            'auto_delist' => false,
            'delisting_method' => 'manual',
            'instructions' => 'Use the lookup tool and click "Request Removal" if listed.',
        ],
        'bl.spamcop.net' => [
            'name' => 'SpamCop',
            'delisting_url' => 'https://www.spamcop.net/bl.shtml',
            'auto_delist' => true,
            'auto_delist_hours' => 24,
            'delisting_method' => 'automatic',
            'instructions' => 'SpamCop listings expire automatically after 24 hours if no new spam is reported.',
        ],
        'cbl.abuseat.org' => [
            'name' => 'CBL',
            'delisting_url' => 'https://www.abuseat.org/lookup.cgi',
            'auto_delist' => true,
            'delisting_method' => 'self_service',
            'instructions' => 'Enter your IP in the lookup tool and click the removal link.',
        ],
        'dnsbl.sorbs.net' => [
            'name' => 'SORBS',
            'delisting_url' => 'http://www.sorbs.net/lookup.shtml',
            'auto_delist' => false,
            'delisting_method' => 'request',
            'instructions' => 'Look up your IP and follow the delisting procedure for your listing type.',
        ],
        'psbl.surriel.com' => [
            'name' => 'PSBL',
            'delisting_url' => 'https://psbl.org/listing',
            'auto_delist' => true,
            'auto_delist_hours' => 48,
            'delisting_method' => 'automatic',
            'instructions' => 'PSBL listings expire automatically after 48 hours.',
        ],
        'dnsbl-1.uceprotect.net' => [
            'name' => 'UCEPROTECT',
            'delisting_url' => 'http://www.uceprotect.net/en/rblcheck.php',
            'auto_delist' => true,
            'auto_delist_hours' => 168,
            'delisting_method' => 'automatic',
            'instructions' => 'Level 1 listings expire after 7 days. Express delisting available for a fee.',
        ],
        'dnsbl.dronebl.org' => [
            'name' => 'DroneBL',
            'delisting_url' => 'https://dronebl.org/lookup',
            'auto_delist' => false,
            'delisting_method' => 'request',
            'instructions' => 'Use the lookup tool and request removal via the RBL lookup form.',
        ],
    ];

    /**
     * Request delisting for an IP from a specific blocklist
     */
    public function requestDelisting(string $ip, string $blocklist): array
    {
        $info = $this->blocklists[$blocklist] ?? null;

        if (!$info) {
            return [
                'status' => 'unknown',
                'message' => "Unknown blocklist: {$blocklist}",
            ];
        }

        Log::info("Processing delisting request for {$ip} from {$blocklist}");

        // Try automated delisting if available
        if ($info['delisting_method'] === 'self_service') {
            return $this->attemptSelfServiceDelisting($ip, $blocklist, $info);
        }

        if ($info['delisting_method'] === 'automatic') {
            return [
                'status' => 'auto_expire',
                'message' => $info['instructions'],
                'request_url' => $info['delisting_url'],
                'auto_delist_hours' => $info['auto_delist_hours'] ?? null,
            ];
        }

        // Manual delisting required
        return [
            'status' => 'manual_required',
            'message' => $info['instructions'],
            'request_url' => $info['delisting_url'],
            'blocklist_name' => $info['name'],
        ];
    }

    /**
     * Attempt self-service delisting (for blocklists that support it)
     */
    protected function attemptSelfServiceDelisting(string $ip, string $blocklist, array $info): array
    {
        // CBL supports automated self-removal
        if ($blocklist === 'cbl.abuseat.org') {
            return $this->attemptCblDelisting($ip);
        }

        // Fall back to manual for other self-service lists
        return [
            'status' => 'manual_required',
            'message' => $info['instructions'],
            'request_url' => $info['delisting_url'],
        ];
    }

    /**
     * Attempt CBL self-service removal
     */
    protected function attemptCblDelisting(string $ip): array
    {
        try {
            // CBL allows automatic lookups - check if listed first
            $lookupUrl = "https://www.abuseat.org/lookup.cgi?ip={$ip}";

            return [
                'status' => 'pending',
                'message' => 'Please visit the removal link to complete the delisting process.',
                'request_url' => $lookupUrl,
                'blocklist_name' => 'CBL',
                'instructions' => 'Click the lookup URL, then click "Remove" if the option is available.',
            ];
        } catch (\Exception $e) {
            Log::error("CBL delisting attempt failed: " . $e->getMessage());

            return [
                'status' => 'failed',
                'message' => 'Failed to process CBL delisting request.',
                'request_url' => 'https://www.abuseat.org/lookup.cgi',
            ];
        }
    }

    /**
     * Check if an IP is still listed on a specific blocklist
     */
    public function checkIfStillListed(string $ip, string $blocklist): bool
    {
        try {
            $reversedIp = implode('.', array_reverse(explode('.', $ip)));
            $query = "{$reversedIp}.{$blocklist}";
            $result = @dns_get_record($query, DNS_A);

            return !empty($result);
        } catch (\Exception $e) {
            Log::warning("Failed to check listing status: " . $e->getMessage());
            return true; // Assume still listed on error
        }
    }

    /**
     * Get delisting URL for a blocklist
     */
    public function getDelistingUrl(string $blocklist): ?string
    {
        return $this->blocklists[$blocklist]['delisting_url'] ?? null;
    }

    /**
     * Get blocklist information
     */
    public function getBlocklistInfo(string $blocklist): ?array
    {
        return $this->blocklists[$blocklist] ?? null;
    }

    /**
     * Get all supported blocklists with delisting info
     */
    public function getAllBlocklists(): array
    {
        return $this->blocklists;
    }

    /**
     * Get delisting instructions for a specific blocklist
     */
    public function getDelistingInstructions(string $blocklist): string
    {
        return $this->blocklists[$blocklist]['instructions']
            ?? 'Visit the blocklist website and follow their removal procedure.';
    }

    /**
     * Batch check delisting status for multiple requests
     */
    public function batchCheckDelistingStatus(array $requests): array
    {
        $results = [];

        foreach ($requests as $request) {
            $ip = $request['ip'];
            $blocklist = $request['blocklist'];

            $isListed = $this->checkIfStillListed($ip, $blocklist);

            $results[] = [
                'ip' => $ip,
                'blocklist' => $blocklist,
                'still_listed' => $isListed,
                'checked_at' => now()->toIso8601String(),
            ];

            // Add small delay to avoid rate limiting
            usleep(100000); // 0.1 second
        }

        return $results;
    }
}
