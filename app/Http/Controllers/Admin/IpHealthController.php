<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BulkReputationCheck;
use App\Jobs\CheckIpReputation;
use App\Jobs\MonitorSubnetReputation;
use App\Models\Subnet;
use App\Models\AbuseReport;
use App\Repositories\Contracts\SubnetRepositoryInterface;
use App\Repositories\Contracts\AbuseReportRepositoryInterface;
use App\Services\IpReputationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IpHealthController extends Controller
{
    public function __construct(
        protected SubnetRepositoryInterface $subnetRepository,
        protected AbuseReportRepositoryInterface $abuseReportRepository,
        protected IpReputationService $reputationService
    ) {}

    public function index(): View
    {
        $subnets = Subnet::with('company')
            ->orderByRaw('CASE WHEN reputation_score < 70 THEN 0 WHEN reputation_score < 85 THEN 1 ELSE 2 END')
            ->orderBy('last_reputation_check', 'asc')
            ->paginate(20);

        $stats = $this->getHealthStats();

        return view('admin.ip-health.index', compact('subnets', 'stats'));
    }

    public function dashboard(): View
    {
        $stats = $this->getHealthStats();

        // Get recent reputation changes
        $recentChanges = Subnet::where('last_reputation_check', '>=', now()->subDays(7))
            ->orderBy('last_reputation_check', 'desc')
            ->limit(20)
            ->get();

        // Get critical issues
        $criticalSubnets = Subnet::where('reputation_score', '<', 50)
            ->with('company')
            ->get();

        // Get recent abuse reports
        $recentAbuse = AbuseReport::with(['subnet', 'lease'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Blocklist distribution
        $blocklistDistribution = $this->getBlocklistDistribution();

        return view('admin.ip-health.dashboard', compact(
            'stats',
            'recentChanges',
            'criticalSubnets',
            'recentAbuse',
            'blocklistDistribution'
        ));
    }

    public function subnetsAtRisk(): View
    {
        $atRiskSubnets = Subnet::where('reputation_score', '<', 85)
            ->with(['company', 'abuseReports' => function ($q) {
                $q->where('status', '!=', 'resolved')->latest();
            }])
            ->orderBy('reputation_score', 'asc')
            ->paginate(20);

        $needingCheck = $this->subnetRepository->getNeedingReputationCheck(48);

        return view('admin.ip-health.at-risk', compact('atRiskSubnets', 'needingCheck'));
    }

    public function scheduleCheck(Request $request): RedirectResponse
    {
        $request->validate([
            'hours_threshold' => 'nullable|integer|min:1|max:168',
            'batch_size' => 'nullable|integer|min:10|max:200',
        ]);

        MonitorSubnetReputation::dispatch(
            $request->get('hours_threshold', 24),
            $request->get('batch_size', 50)
        )->onQueue('reputation');

        return back()->with('success', __('Scheduled reputation monitoring check has been queued.'));
    }

    public function bulkCheck(Request $request): RedirectResponse
    {
        $request->validate([
            'subnet_ids' => 'required|array',
            'subnet_ids.*' => 'exists:subnets,id',
        ]);

        $subnetIds = $request->subnet_ids;

        if (count($subnetIds) > 100) {
            return back()->with('error', __('Cannot process more than 100 subnets at once.'));
        }

        BulkReputationCheck::dispatch($subnetIds, auth()->id())->onQueue('reputation');

        return back()->with('success', __(':count subnets queued for reputation check.', ['count' => count($subnetIds)]));
    }

    protected function getHealthStats(): array
    {
        $total = Subnet::count();
        $clean = Subnet::where('reputation_score', '>=', 85)->count();
        $warning = Subnet::whereBetween('reputation_score', [70, 84])->count();
        $critical = Subnet::where('reputation_score', '<', 70)->count();
        $unchecked = Subnet::whereNull('last_reputation_check')->count();
        $stale = Subnet::where('last_reputation_check', '<', now()->subHours(48))->count();

        $openAbuse = AbuseReport::whereIn('status', ['open', 'investigating'])->count();
        $criticalAbuse = AbuseReport::where('severity', 'critical')
            ->whereIn('status', ['open', 'investigating'])
            ->count();

        $averageScore = Subnet::whereNotNull('reputation_score')->avg('reputation_score');

        return [
            'total' => $total,
            'clean' => $clean,
            'clean_percentage' => $total > 0 ? round(($clean / $total) * 100, 1) : 0,
            'warning' => $warning,
            'critical' => $critical,
            'unchecked' => $unchecked,
            'stale' => $stale,
            'open_abuse' => $openAbuse,
            'critical_abuse' => $criticalAbuse,
            'average_score' => round($averageScore ?? 0, 1),
        ];
    }

    protected function getBlocklistDistribution(): array
    {
        $distribution = [];
        $subnets = Subnet::whereNotNull('blocklist_results')->get();

        foreach ($subnets as $subnet) {
            if (!is_array($subnet->blocklist_results)) {
                continue;
            }

            foreach ($subnet->blocklist_results as $blocklist => $result) {
                if (is_array($result) && ($result['listed'] ?? false)) {
                    if (!isset($distribution[$blocklist])) {
                        $distribution[$blocklist] = 0;
                    }
                    $distribution[$blocklist]++;
                }
            }
        }

        arsort($distribution);

        return $distribution;
    }
}
