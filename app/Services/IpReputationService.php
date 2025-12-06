<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IpReputationService
{
    /**
     * DNS Blocklists to check (DNSBL)
     * Format: domain => weight (for scoring)
     */
    protected array $dnsBlocklists = [
        // Critical blocklists - high impact
        'zen.spamhaus.org' => 30,          // Spamhaus ZEN (SBL+XBL+PBL)
        'b.barracudacentral.org' => 25,    // Barracuda
        'bl.spamcop.net' => 20,            // SpamCop
        'dnsbl.sorbs.net' => 15,           // SORBS

        // Important blocklists
        'psbl.surriel.com' => 10,          // PSBL
        'dnsbl-1.uceprotect.net' => 10,    // UCEPROTECT Level 1
        'spam.dnsbl.anonmails.de' => 8,    // AnonMails
        'cbl.abuseat.org' => 15,           // CBL

        // Additional blocklists
        'dnsbl.dronebl.org' => 8,          // DroneRL
        'rbl.interserver.net' => 5,        // InterServer
        'all.s5h.net' => 5,                // s5h.net
    ];

    /**
     * Check IP reputation against all blocklists
     */
    public function checkReputation(string $ip): array
    {
        $cacheKey = "ip_reputation_{$ip}";

        return Cache::remember($cacheKey, 3600, function () use ($ip) {
            $results = [];
            $totalScore = 100;
            $blockedCount = 0;

            foreach ($this->dnsBlocklists as $blocklist => $weight) {
                $isListed = $this->checkDnsbl($ip, $blocklist);
                $results[$blocklist] = [
                    'listed' => $isListed,
                    'weight' => $weight,
                ];

                if ($isListed) {
                    $totalScore -= $weight;
                    $blockedCount++;
                }
            }

            // Ensure score doesn't go below 0
            $totalScore = max(0, $totalScore);

            // Determine status
            $status = 'clean';
            if ($totalScore < 50) {
                $status = 'critical';
            } elseif ($totalScore < 70) {
                $status = 'warning';
            } elseif ($totalScore < 85) {
                $status = 'notice';
            }

            return [
                'ip' => $ip,
                'score' => $totalScore,
                'status' => $status,
                'blocked_count' => $blockedCount,
                'total_checked' => count($this->dnsBlocklists),
                'blocklists' => $results,
                'checked_at' => now()->toIso8601String(),
                'is_clean' => $status === 'clean',
                'can_list' => $totalScore >= 70,
            ];
        });
    }

    /**
     * Check a single IP against a DNSBL
     */
    protected function checkDnsbl(string $ip, string $blocklist): bool
    {
        try {
            // Reverse the IP octets
            $reversedIp = implode('.', array_reverse(explode('.', $ip)));

            // Query the DNSBL
            $query = "{$reversedIp}.{$blocklist}";
            $result = @dns_get_record($query, DNS_A);

            // If we get a result, the IP is listed
            return !empty($result);
        } catch (\Exception $e) {
            Log::warning("DNSBL check failed for {$ip} on {$blocklist}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check IP against AbuseIPDB (requires API key)
     */
    public function checkAbuseIpDb(string $ip): ?array
    {
        $apiKey = config('services.abuseipdb.key');

        if (!$apiKey) {
            return null;
        }

        $cacheKey = "abuseipdb_{$ip}";

        return Cache::remember($cacheKey, 3600, function () use ($ip, $apiKey) {
            try {
                $response = Http::withHeaders([
                    'Key' => $apiKey,
                    'Accept' => 'application/json',
                ])->get('https://api.abuseipdb.com/api/v2/check', [
                    'ipAddress' => $ip,
                    'maxAgeInDays' => 90,
                    'verbose' => true,
                ]);

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? null;

                    if ($data) {
                        return [
                            'ip' => $data['ipAddress'],
                            'is_public' => $data['isPublic'],
                            'abuse_confidence_score' => $data['abuseConfidenceScore'],
                            'country' => $data['countryCode'],
                            'usage_type' => $data['usageType'],
                            'isp' => $data['isp'],
                            'domain' => $data['domain'],
                            'total_reports' => $data['totalReports'],
                            'num_distinct_users' => $data['numDistinctUsers'],
                            'last_reported_at' => $data['lastReportedAt'],
                        ];
                    }
                }

                return null;
            } catch (\Exception $e) {
                Log::error("AbuseIPDB check failed: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get blocklist details for listed IPs
     */
    public function getBlocklistDetails(string $ip): array
    {
        $result = $this->checkReputation($ip);
        $details = [];

        foreach ($result['blocklists'] as $blocklist => $data) {
            if ($data['listed']) {
                $details[] = [
                    'blocklist' => $blocklist,
                    'weight' => $data['weight'],
                    'severity' => $this->getBlocklistSeverity($blocklist),
                    'delisting_url' => $this->getDelistingUrl($blocklist),
                ];
            }
        }

        return $details;
    }

    /**
     * Get severity level for a blocklist
     */
    protected function getBlocklistSeverity(string $blocklist): string
    {
        $critical = ['zen.spamhaus.org', 'b.barracudacentral.org', 'cbl.abuseat.org'];
        $high = ['bl.spamcop.net', 'dnsbl.sorbs.net', 'dnsbl-1.uceprotect.net'];

        if (in_array($blocklist, $critical)) {
            return 'critical';
        }

        if (in_array($blocklist, $high)) {
            return 'high';
        }

        return 'medium';
    }

    /**
     * Get delisting URL for a blocklist
     */
    protected function getDelistingUrl(string $blocklist): ?string
    {
        $urls = [
            'zen.spamhaus.org' => 'https://www.spamhaus.org/lookup/',
            'b.barracudacentral.org' => 'https://www.barracudacentral.org/lookups',
            'bl.spamcop.net' => 'https://www.spamcop.net/bl.shtml',
            'dnsbl.sorbs.net' => 'http://www.sorbs.net/lookup.shtml',
            'psbl.surriel.com' => 'https://psbl.org/listing',
            'dnsbl-1.uceprotect.net' => 'http://www.uceprotect.net/en/rblcheck.php',
            'cbl.abuseat.org' => 'https://www.abuseat.org/lookup.cgi',
            'dnsbl.dronebl.org' => 'https://dronebl.org/lookup',
        ];

        return $urls[$blocklist] ?? null;
    }

    /**
     * Check if an IP range has clean reputation
     */
    public function checkSubnetReputation(string $ip, int $cidr): array
    {
        // For larger subnets, sample a few IPs
        $ipCount = pow(2, 32 - $cidr);
        $samplesToCheck = min(5, $ipCount);

        $results = [];
        $totalScore = 0;

        // Convert IP to long
        $ipLong = ip2long($ip);

        for ($i = 0; $i < $samplesToCheck; $i++) {
            // Calculate sample IP (evenly distributed across range)
            $offset = intval($i * ($ipCount / $samplesToCheck));
            $sampleIp = long2ip($ipLong + $offset);

            $check = $this->checkReputation($sampleIp);
            $results[$sampleIp] = $check;
            $totalScore += $check['score'];
        }

        $averageScore = $totalScore / $samplesToCheck;

        return [
            'subnet' => "{$ip}/{$cidr}",
            'average_score' => round($averageScore),
            'samples_checked' => $samplesToCheck,
            'total_ips' => $ipCount,
            'sample_results' => $results,
            'is_clean' => $averageScore >= 85,
            'can_list' => $averageScore >= 70,
        ];
    }

    /**
     * Clear cache for an IP
     */
    public function clearCache(string $ip): void
    {
        Cache::forget("ip_reputation_{$ip}");
        Cache::forget("abuseipdb_{$ip}");
    }

    /**
     * Get a summary report for an IP
     */
    public function getSummaryReport(string $ip): array
    {
        $reputation = $this->checkReputation($ip);
        $abuseIpDb = $this->checkAbuseIpDb($ip);
        $blocklistDetails = $this->getBlocklistDetails($ip);

        return [
            'ip' => $ip,
            'overall_score' => $reputation['score'],
            'overall_status' => $reputation['status'],
            'blocklist_count' => $reputation['blocked_count'],
            'blocklist_details' => $blocklistDetails,
            'abuseipdb' => $abuseIpDb,
            'recommendation' => $this->getRecommendation($reputation, $abuseIpDb),
            'can_be_listed' => $reputation['can_list'] && (!$abuseIpDb || $abuseIpDb['abuse_confidence_score'] < 25),
        ];
    }

    /**
     * Get recommendation based on reputation
     */
    protected function getRecommendation(array $reputation, ?array $abuseIpDb): string
    {
        if ($reputation['status'] === 'critical') {
            return 'This IP has critical reputation issues and cannot be listed. Please clean the IP from all blocklists before attempting to list.';
        }

        if ($reputation['status'] === 'warning') {
            return 'This IP has reputation issues that should be addressed. Delisting from major blocklists is recommended before listing.';
        }

        if ($abuseIpDb && $abuseIpDb['abuse_confidence_score'] > 50) {
            return 'This IP has a high abuse score on AbuseIPDB. Review and resolve any ongoing abuse issues.';
        }

        if ($reputation['status'] === 'notice') {
            return 'This IP has minor reputation issues. Listing is allowed but delisting from blocklists is recommended.';
        }

        return 'This IP has a clean reputation and can be listed in the marketplace.';
    }
}
