<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subnet;
use App\Services\IpReputationService;
use App\Services\WhoisService;
use Illuminate\Http\Request;

class SubnetManagementController extends Controller
{
    protected IpReputationService $reputationService;
    protected WhoisService $whoisService;

    public function __construct(IpReputationService $reputationService, WhoisService $whoisService)
    {
        $this->reputationService = $reputationService;
        $this->whoisService = $whoisService;
    }

    public function index(Request $request)
    {
        $query = Subnet::with('company')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by RIR
        if ($request->has('rir') && $request->rir !== 'all') {
            $query->where('rir', $request->rir);
        }

        // Filter by reputation score
        if ($request->has('reputation') && $request->reputation !== 'all') {
            switch ($request->reputation) {
                case 'clean':
                    $query->where('reputation_score', '>=', 85);
                    break;
                case 'warning':
                    $query->whereBetween('reputation_score', [50, 84]);
                    break;
                case 'critical':
                    $query->where('reputation_score', '<', 50);
                    break;
            }
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $subnets = $query->paginate(20);

        return view('admin.subnets.index', compact('subnets'));
    }

    public function show(Subnet $subnet)
    {
        $subnet->load(['company.user', 'leases.lesseeCompany', 'abuseReports']);

        // Get WHOIS data
        $whoisData = $this->whoisService->query($subnet->ip_address, $subnet->rir);

        // Get reputation details
        $reputationDetails = null;
        if ($subnet->reputation_score !== null) {
            $reputationDetails = $this->reputationService->getBlocklistDetails($subnet->ip_address);
        }

        return view('admin.subnets.show', compact('subnet', 'whoisData', 'reputationDetails'));
    }

    public function verify(Subnet $subnet)
    {
        if ($subnet->ownership_verified_at) {
            return back()->with('info', 'Subnet is already verified.');
        }

        $subnet->update([
            'ownership_verified_at' => now(),
            'status' => 'available',
        ]);

        return back()->with('success', "Subnet {$subnet->cidr_notation} has been manually verified and is now available.");
    }

    public function suspend(Request $request, Subnet $subnet)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $subnet->update([
            'status' => 'suspended',
            'description' => $subnet->description . "\n\n[SUSPENDED: " . $request->reason . "]",
        ]);

        return back()->with('success', "Subnet {$subnet->cidr_notation} has been suspended.");
    }

    public function unsuspend(Subnet $subnet)
    {
        if ($subnet->status !== 'suspended') {
            return back()->with('error', 'Subnet is not suspended.');
        }

        $newStatus = $subnet->ownership_verified_at ? 'available' : 'pending_verification';

        $subnet->update([
            'status' => $newStatus,
        ]);

        return back()->with('success', "Subnet {$subnet->cidr_notation} has been unsuspended. Status: {$newStatus}");
    }

    public function checkReputation(Subnet $subnet)
    {
        $report = $this->reputationService->getSummaryReport($subnet->ip_address);

        $subnet->update([
            'reputation_score' => $report['overall_score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $report['blocklist_details'],
        ]);

        return back()->with('success', "Reputation check completed. Score: {$report['overall_score']}/100");
    }

    public function bulkReputationCheck(Request $request)
    {
        $query = Subnet::query();

        // Only check subnets that haven't been checked in 24 hours
        $query->where(function ($q) {
            $q->whereNull('last_reputation_check')
              ->orWhere('last_reputation_check', '<', now()->subDay());
        });

        $subnets = $query->limit(50)->get();

        $checked = 0;
        $issues = 0;

        foreach ($subnets as $subnet) {
            $report = $this->reputationService->checkReputation($subnet->ip_address);

            $subnet->update([
                'reputation_score' => $report['score'],
                'last_reputation_check' => now(),
                'blocklist_results' => $report['blocklists'],
            ]);

            $checked++;

            if (!$report['can_list'] && $subnet->status === 'available') {
                $subnet->update(['status' => 'suspended']);
                $issues++;
            }
        }

        return back()->with('success', "Checked {$checked} subnets. {$issues} subnets suspended due to reputation issues.");
    }
}
