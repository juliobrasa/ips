<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReputationResource;
use App\Jobs\BulkReputationCheck;
use App\Models\Subnet;
use App\Services\IpReputationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReputationController extends Controller
{
    public function __construct(
        protected IpReputationService $reputationService
    ) {}

    public function check(Request $request, string $ip): JsonResponse
    {
        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return response()->json([
                'message' => __('Invalid IPv4 address.'),
            ], 422);
        }

        // Clear cache if requested
        if ($request->boolean('refresh')) {
            $this->reputationService->clearCache($ip);
        }

        $report = $this->reputationService->getSummaryReport($ip);

        return response()->json([
            'data' => new ReputationResource($report),
        ]);
    }

    public function checkSubnet(Subnet $subnet): JsonResponse
    {
        $this->authorize('view', $subnet);

        $report = $this->reputationService->checkSubnetReputation(
            $subnet->ip_address,
            $subnet->cidr
        );

        return response()->json([
            'subnet' => $subnet->cidr_notation,
            'average_score' => $report['average_score'],
            'samples_checked' => $report['samples_checked'],
            'total_ips' => $report['total_ips'],
            'is_clean' => $report['is_clean'],
            'can_list' => $report['can_list'],
            'sample_results' => collect($report['sample_results'])->map(function ($result, $ip) {
                return [
                    'ip' => $ip,
                    'score' => $result['score'],
                    'status' => $result['status'],
                    'blocked_count' => $result['blocked_count'],
                ];
            })->values(),
        ]);
    }

    public function listBlocklists(): JsonResponse
    {
        $blocklists = [
            [
                'name' => 'Spamhaus ZEN',
                'domain' => 'zen.spamhaus.org',
                'weight' => 30,
                'severity' => 'critical',
                'description' => 'Spamhaus Block List - Combines SBL, XBL, and PBL for comprehensive spam blocking',
                'delisting_url' => 'https://www.spamhaus.org/lookup/',
            ],
            [
                'name' => 'Barracuda',
                'domain' => 'b.barracudacentral.org',
                'weight' => 25,
                'severity' => 'critical',
                'description' => 'Barracuda Reputation Block List',
                'delisting_url' => 'https://www.barracudacentral.org/lookups',
            ],
            [
                'name' => 'SpamCop',
                'domain' => 'bl.spamcop.net',
                'weight' => 20,
                'severity' => 'high',
                'description' => 'SpamCop Blocking List',
                'delisting_url' => 'https://www.spamcop.net/bl.shtml',
            ],
            [
                'name' => 'CBL',
                'domain' => 'cbl.abuseat.org',
                'weight' => 15,
                'severity' => 'critical',
                'description' => 'Composite Blocking List - Detects spam-sending IPs',
                'delisting_url' => 'https://www.abuseat.org/lookup.cgi',
            ],
            [
                'name' => 'SORBS',
                'domain' => 'dnsbl.sorbs.net',
                'weight' => 15,
                'severity' => 'high',
                'description' => 'Spam and Open Relay Blocking System',
                'delisting_url' => 'http://www.sorbs.net/lookup.shtml',
            ],
            [
                'name' => 'PSBL',
                'domain' => 'psbl.surriel.com',
                'weight' => 10,
                'severity' => 'medium',
                'description' => 'Passive Spam Block List',
                'delisting_url' => 'https://psbl.org/listing',
            ],
            [
                'name' => 'UCEPROTECT Level 1',
                'domain' => 'dnsbl-1.uceprotect.net',
                'weight' => 10,
                'severity' => 'high',
                'description' => 'UCEPROTECT Network - Individual IP listings',
                'delisting_url' => 'http://www.uceprotect.net/en/rblcheck.php',
            ],
            [
                'name' => 'DroneRL',
                'domain' => 'dnsbl.dronebl.org',
                'weight' => 8,
                'severity' => 'medium',
                'description' => 'DroneBL - IPs of DDoS drones and compromised machines',
                'delisting_url' => 'https://dronebl.org/lookup',
            ],
            [
                'name' => 'AnonMails',
                'domain' => 'spam.dnsbl.anonmails.de',
                'weight' => 8,
                'severity' => 'medium',
                'description' => 'AnonMails Spam DNSBL',
                'delisting_url' => null,
            ],
            [
                'name' => 'InterServer',
                'domain' => 'rbl.interserver.net',
                'weight' => 5,
                'severity' => 'low',
                'description' => 'InterServer RBL',
                'delisting_url' => null,
            ],
            [
                'name' => 's5h.net',
                'domain' => 'all.s5h.net',
                'weight' => 5,
                'severity' => 'low',
                'description' => 's5h.net all-in-one list',
                'delisting_url' => null,
            ],
        ];

        return response()->json([
            'data' => $blocklists,
            'total' => count($blocklists),
            'max_score' => 100,
            'threshold_clean' => 85,
            'threshold_listable' => 70,
        ]);
    }

    // Admin method
    public function bulkCheck(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subnet_ids' => 'required|array|min:1|max:100',
            'subnet_ids.*' => 'exists:subnets,id',
        ]);

        BulkReputationCheck::dispatch(
            $validated['subnet_ids'],
            $request->user()->id
        )->onQueue('reputation');

        return response()->json([
            'message' => __('Bulk reputation check started.'),
            'count' => count($validated['subnet_ids']),
        ]);
    }
}
