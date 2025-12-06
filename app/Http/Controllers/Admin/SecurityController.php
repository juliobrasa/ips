<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbuseReport;
use App\Models\Subnet;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SecurityController extends Controller
{
    /**
     * Security Dashboard - Overview of IP reputation and abuse status
     */
    public function index()
    {
        $stats = [
            'open_reports' => AbuseReport::open()->count(),
            'investigating_reports' => AbuseReport::investigating()->count(),
            'critical_reports' => AbuseReport::open()->critical()->count(),
            'resolved_this_month' => AbuseReport::resolved()
                ->whereMonth('resolved_at', now()->month)
                ->count(),
            'total_subnets' => Subnet::count(),
            'clean_subnets' => Subnet::where('reputation_score', '>=', 80)->count(),
            'warning_subnets' => Subnet::whereBetween('reputation_score', [50, 79])->count(),
            'blocklisted_subnets' => Subnet::where('reputation_score', '<', 50)->count(),
        ];

        $recentReports = AbuseReport::with(['subnet', 'lease'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $subnetsNeedingAttention = Subnet::where('reputation_score', '<', 80)
            ->with('company')
            ->orderBy('reputation_score', 'asc')
            ->limit(10)
            ->get();

        $reportsByType = AbuseReport::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('admin.security.index', compact(
            'stats',
            'recentReports',
            'subnetsNeedingAttention',
            'reportsByType'
        ));
    }

    /**
     * Blocklist Check Tool - Manual IP/Subnet checking
     */
    public function blocklistCheck()
    {
        $recentChecks = Cache::get('recent_blocklist_checks', []);

        return view('admin.security.blocklist-check', compact('recentChecks'));
    }

    /**
     * Check a single IP against multiple blocklists
     */
    public function checkIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
        ]);

        $ip = $request->ip;
        $results = $this->performBlocklistCheck($ip);

        // Store in recent checks cache
        $recentChecks = Cache::get('recent_blocklist_checks', []);
        array_unshift($recentChecks, [
            'ip' => $ip,
            'results' => $results,
            'checked_at' => now()->toDateTimeString(),
        ]);
        $recentChecks = array_slice($recentChecks, 0, 20);
        Cache::put('recent_blocklist_checks', $recentChecks, now()->addDay());

        return response()->json([
            'success' => true,
            'ip' => $ip,
            'results' => $results,
            'summary' => $this->summarizeResults($results),
        ]);
    }

    /**
     * Check a subnet (batch check first and last IPs)
     */
    public function checkSubnet(Request $request)
    {
        $request->validate([
            'subnet_id' => 'required|exists:subnets,id',
        ]);

        $subnet = Subnet::findOrFail($request->subnet_id);
        $results = $this->checkSubnetReputation($subnet);

        // Update subnet reputation
        $subnet->update([
            'reputation_score' => $results['score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $results['details'],
        ]);

        return response()->json([
            'success' => true,
            'subnet' => $subnet->cidr_notation,
            'results' => $results,
        ]);
    }

    /**
     * List all abuse reports with filtering
     */
    public function abuseReports(Request $request)
    {
        $query = AbuseReport::with(['subnet', 'lease', 'resolvedByUser']);

        // Apply filters
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->severity && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'open' => AbuseReport::open()->count(),
            'investigating' => AbuseReport::investigating()->count(),
            'resolved' => AbuseReport::resolved()->count(),
            'dismissed' => AbuseReport::where('status', 'dismissed')->count(),
        ];

        return view('admin.security.abuse-reports', compact('reports', 'stats'));
    }

    /**
     * Show single abuse report detail
     */
    public function showAbuseReport(AbuseReport $report)
    {
        $report->load(['subnet.company', 'lease.lesseeCompany', 'resolvedByUser']);

        $relatedReports = AbuseReport::where('subnet_id', $report->subnet_id)
            ->where('id', '!=', $report->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.security.abuse-report-show', compact('report', 'relatedReports'));
    }

    /**
     * Resolve an abuse report
     */
    public function resolveAbuseReport(Request $request, AbuseReport $report)
    {
        $request->validate([
            'resolution_notes' => 'required|string|min:10',
        ]);

        $report->resolve(auth()->id(), $request->resolution_notes);

        return redirect()->route('admin.security.abuse-reports')
            ->with('success', 'Abuse report resolved successfully.');
    }

    /**
     * Dismiss an abuse report
     */
    public function dismissAbuseReport(Request $request, AbuseReport $report)
    {
        $request->validate([
            'resolution_notes' => 'required|string|min:10',
        ]);

        $report->dismiss(auth()->id(), $request->resolution_notes);

        return redirect()->route('admin.security.abuse-reports')
            ->with('success', 'Abuse report dismissed.');
    }

    /**
     * Perform blocklist check against multiple sources
     */
    private function performBlocklistCheck(string $ip): array
    {
        $results = [];

        // AbuseIPDB Check
        $results['abuseipdb'] = $this->checkAbuseIPDB($ip);

        // DNS-based Blocklist Checks
        $dnsBlocklists = [
            'zen.spamhaus.org' => 'Spamhaus ZEN',
            'bl.spamcop.net' => 'SpamCop',
            'b.barracudacentral.org' => 'Barracuda',
            'dnsbl.sorbs.net' => 'SORBS',
            'all.spamrats.com' => 'SpamRATS',
        ];

        foreach ($dnsBlocklists as $dnsbl => $name) {
            $results[$dnsbl] = $this->checkDnsBlocklist($ip, $dnsbl, $name);
        }

        return $results;
    }

    /**
     * Check IP against AbuseIPDB
     */
    private function checkAbuseIPDB(string $ip): array
    {
        $apiKey = config('services.abuseipdb.key');

        if (empty($apiKey)) {
            return [
                'name' => 'AbuseIPDB',
                'listed' => null,
                'error' => 'API key not configured',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Key' => $apiKey,
                'Accept' => 'application/json',
            ])->get('https://api.abuseipdb.com/api/v2/check', [
                'ipAddress' => $ip,
                'maxAgeInDays' => 90,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];
                return [
                    'name' => 'AbuseIPDB',
                    'listed' => $data['abuseConfidenceScore'] > 0,
                    'score' => $data['abuseConfidenceScore'],
                    'total_reports' => $data['totalReports'],
                    'country' => $data['countryCode'] ?? null,
                    'isp' => $data['isp'] ?? null,
                    'domain' => $data['domain'] ?? null,
                    'is_whitelisted' => $data['isWhitelisted'] ?? false,
                ];
            }

            return [
                'name' => 'AbuseIPDB',
                'listed' => null,
                'error' => 'API request failed',
            ];
        } catch (\Exception $e) {
            Log::error('AbuseIPDB check failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return [
                'name' => 'AbuseIPDB',
                'listed' => null,
                'error' => 'Connection error',
            ];
        }
    }

    /**
     * Check IP against DNS-based blocklist
     */
    private function checkDnsBlocklist(string $ip, string $dnsbl, string $name): array
    {
        // Reverse the IP octets
        $reversedIp = implode('.', array_reverse(explode('.', $ip)));
        $query = $reversedIp . '.' . $dnsbl;

        // Perform DNS lookup
        $result = @dns_get_record($query, DNS_A);

        return [
            'name' => $name,
            'dnsbl' => $dnsbl,
            'listed' => !empty($result),
            'response' => !empty($result) ? $result[0]['ip'] ?? null : null,
        ];
    }

    /**
     * Check subnet reputation by sampling IPs
     */
    private function checkSubnetReputation(Subnet $subnet): array
    {
        $baseIp = ip2long($subnet->ip_address);
        $ipCount = pow(2, 32 - $subnet->cidr);

        // Sample first IP, middle IP, and last usable IP
        $sampleIps = [
            long2ip($baseIp + 1),           // First usable
            long2ip($baseIp + intval($ipCount / 2)), // Middle
            long2ip($baseIp + $ipCount - 2), // Last usable
        ];

        $allResults = [];
        $totalScore = 0;
        $checksCount = 0;

        foreach ($sampleIps as $ip) {
            $results = $this->performBlocklistCheck($ip);
            $allResults[$ip] = $results;

            // Calculate score based on results
            foreach ($results as $check) {
                if (isset($check['listed'])) {
                    $checksCount++;
                    if (!$check['listed']) {
                        $totalScore += 100;
                    } elseif (isset($check['score'])) {
                        // For AbuseIPDB, invert the confidence score
                        $totalScore += (100 - $check['score']);
                    }
                }
            }
        }

        $averageScore = $checksCount > 0 ? round($totalScore / $checksCount) : 100;

        return [
            'score' => $averageScore,
            'sample_ips' => $sampleIps,
            'details' => $allResults,
        ];
    }

    /**
     * Summarize blocklist check results
     */
    private function summarizeResults(array $results): array
    {
        $listed = 0;
        $clean = 0;
        $errors = 0;

        foreach ($results as $result) {
            if ($result['listed'] === true) {
                $listed++;
            } elseif ($result['listed'] === false) {
                $clean++;
            } else {
                $errors++;
            }
        }

        $total = count($results);
        $score = $total > 0 ? round(($clean / $total) * 100) : 0;

        return [
            'total_checks' => $total,
            'listed' => $listed,
            'clean' => $clean,
            'errors' => $errors,
            'score' => $score,
            'status' => $listed > 0 ? 'blocklisted' : ($errors === $total ? 'unknown' : 'clean'),
        ];
    }
}
