<?php

namespace App\Services;

use App\Models\Subnet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IpQualityService
{
    protected array $blacklists = [
        'spamhaus_zen' => 'zen.spamhaus.org',
        'spamhaus_sbl' => 'sbl.spamhaus.org',
        'spamcop' => 'bl.spamcop.net',
        'barracuda' => 'b.barracudacentral.org',
        'sorbs' => 'dnsbl.sorbs.net',
        'uceprotect' => 'dnsbl-1.uceprotect.net',
        'spamrats' => 'noptr.spamrats.com',
    ];

    /**
     * Calculate quality score for an IP
     */
    public function getIpScore(string $ip): array
    {
        $cacheKey = "ip_quality:{$ip}";

        return Cache::remember($cacheKey, 3600, function () use ($ip) {
            $score = 100;
            $issues = [];
            $checks = [];

            // Check blacklists
            $blacklistResults = $this->checkBlacklists($ip);
            $checks['blacklists'] = $blacklistResults;

            foreach ($blacklistResults as $list => $listed) {
                if ($listed) {
                    $score -= 15;
                    $issues[] = "Listed on {$list}";
                }
            }

            // Check if it's a datacenter IP
            $isDatacenter = $this->isDatacenterIp($ip);
            $checks['is_datacenter'] = $isDatacenter;

            // Check reverse DNS
            $reverseDns = $this->getReverseDns($ip);
            $checks['reverse_dns'] = $reverseDns;
            if (!$reverseDns) {
                $score -= 5;
                $issues[] = 'No reverse DNS';
            }

            // Get abuse contact
            $abuseContact = $this->getAbuseContact($ip);
            $checks['abuse_contact'] = $abuseContact;

            // Calculate final score
            $score = max(0, min(100, $score));

            return [
                'ip' => $ip,
                'score' => $score,
                'grade' => $this->scoreToGrade($score),
                'issues' => $issues,
                'checks' => $checks,
                'checked_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Get quality score for a subnet (samples IPs)
     */
    public function getSubnetScore(string $cidr): array
    {
        $cacheKey = "subnet_quality:{$cidr}";

        return Cache::remember($cacheKey, 3600, function () use ($cidr) {
            // Parse CIDR
            [$network, $prefix] = explode('/', $cidr);
            $prefix = (int) $prefix;

            // Get sample IPs based on subnet size
            $sampleIps = $this->getSampleIps($network, $prefix);
            $scores = [];

            foreach ($sampleIps as $ip) {
                $result = $this->getIpScore($ip);
                $scores[] = $result['score'];
            }

            $avgScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;

            return [
                'cidr' => $cidr,
                'average_score' => round($avgScore, 1),
                'grade' => $this->scoreToGrade($avgScore),
                'samples_checked' => count($sampleIps),
                'min_score' => min($scores) ?: 0,
                'max_score' => max($scores) ?: 0,
                'checked_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Check IP against blacklists
     */
    public function checkBlacklists(string $ip): array
    {
        $results = [];
        $reversed = implode('.', array_reverse(explode('.', $ip)));

        foreach ($this->blacklists as $name => $dnsbl) {
            $lookup = "{$reversed}.{$dnsbl}";
            $results[$name] = $this->dnsblLookup($lookup);
        }

        return $results;
    }

    /**
     * Perform DNSBL lookup
     */
    protected function dnsblLookup(string $lookup): bool
    {
        $result = @dns_get_record($lookup, DNS_A);
        return !empty($result);
    }

    /**
     * Check if IP is from a datacenter
     */
    public function isDatacenterIp(string $ip): bool
    {
        // Common datacenter ASN indicators
        $reverseDns = $this->getReverseDns($ip);

        if (!$reverseDns) {
            return false;
        }

        $datacenterIndicators = [
            'amazon', 'aws', 'azure', 'google', 'digitalocean',
            'linode', 'vultr', 'ovh', 'hetzner', 'contabo',
            'datacenter', 'hosting', 'server', 'vps', 'cloud',
        ];

        $reverseLower = strtolower($reverseDns);

        foreach ($datacenterIndicators as $indicator) {
            if (str_contains($reverseLower, $indicator)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get reverse DNS for IP
     */
    public function getReverseDns(string $ip): ?string
    {
        $host = @gethostbyaddr($ip);
        return ($host && $host !== $ip) ? $host : null;
    }

    /**
     * Get abuse contact for IP
     */
    public function getAbuseContact(string $ip): ?string
    {
        try {
            $response = Http::timeout(10)->get("https://stat.ripe.net/data/abuse-contact-finder/data.json", [
                'resource' => $ip,
                'sourceapp' => 'ips-marketplace',
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $contacts = $data['abuse_contacts'] ?? [];
                return $contacts[0] ?? null;
            }
        } catch (\Exception $e) {
            Log::debug('Abuse contact lookup failed', ['ip' => $ip]);
        }

        return null;
    }

    /**
     * Get sample IPs from subnet
     */
    protected function getSampleIps(string $network, int $prefix): array
    {
        $totalIps = pow(2, 32 - $prefix);
        $sampleSize = min(10, max(1, (int) ($totalIps / 256)));

        $networkLong = ip2long($network);
        $samples = [];

        $step = max(1, (int) ($totalIps / $sampleSize));

        for ($i = 0; $i < $sampleSize; $i++) {
            $offset = $step * $i + 1; // Skip network address
            if ($offset < $totalIps - 1) { // Skip broadcast
                $samples[] = long2ip($networkLong + $offset);
            }
        }

        return $samples;
    }

    /**
     * Convert score to grade
     */
    protected function scoreToGrade(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F',
        };
    }

    /**
     * Batch check quality for multiple subnets
     */
    public function batchCheck(array $cidrs): array
    {
        $results = [];

        foreach ($cidrs as $cidr) {
            $results[$cidr] = $this->getSubnetScore($cidr);
        }

        return $results;
    }

    /**
     * Store quality score in database
     */
    public function storeScore(Subnet $subnet, array $scoreData): void
    {
        DB::table('ip_quality_scores')->updateOrInsert(
            ['subnet_id' => $subnet->id],
            [
                'score' => $scoreData['average_score'],
                'grade' => $scoreData['grade'],
                'issues' => json_encode($scoreData['issues'] ?? []),
                'raw_data' => json_encode($scoreData),
                'checked_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Update subnet reputation score
        $subnet->update([
            'reputation_score' => (int) $scoreData['average_score'],
            'reputation_checked_at' => now(),
        ]);
    }

    /**
     * Get quality summary for user
     */
    public function getUserQualitySummary(int $userId): array
    {
        $scores = DB::table('ip_quality_scores as iqs')
            ->join('subnets as s', 's.id', '=', 'iqs.subnet_id')
            ->where('s.user_id', $userId)
            ->selectRaw('
                AVG(iqs.score) as avg_score,
                MIN(iqs.score) as min_score,
                MAX(iqs.score) as max_score,
                COUNT(*) as total,
                SUM(CASE WHEN iqs.grade = "A" THEN 1 ELSE 0 END) as grade_a,
                SUM(CASE WHEN iqs.grade = "B" THEN 1 ELSE 0 END) as grade_b,
                SUM(CASE WHEN iqs.grade = "C" THEN 1 ELSE 0 END) as grade_c,
                SUM(CASE WHEN iqs.grade = "D" THEN 1 ELSE 0 END) as grade_d,
                SUM(CASE WHEN iqs.grade = "F" THEN 1 ELSE 0 END) as grade_f
            ')
            ->first();

        return [
            'average_score' => round($scores->avg_score ?? 0, 1),
            'min_score' => $scores->min_score ?? 0,
            'max_score' => $scores->max_score ?? 0,
            'total_checked' => $scores->total ?? 0,
            'by_grade' => [
                'A' => $scores->grade_a ?? 0,
                'B' => $scores->grade_b ?? 0,
                'C' => $scores->grade_c ?? 0,
                'D' => $scores->grade_d ?? 0,
                'F' => $scores->grade_f ?? 0,
            ],
        ];
    }
}
