<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Subnet;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get dashboard overview stats
     */
    public function getDashboardStats(?int $companyId = null): array
    {
        $query = fn($model) => $companyId ? $model::where('company_id', $companyId) : $model::query();

        return [
            'subnets' => [
                'total' => $query(Subnet::class)->count(),
                'available' => $query(Subnet::class)->where('status', 'available')->count(),
                'leased' => $query(Subnet::class)->where('status', 'leased')->count(),
                'pending' => $query(Subnet::class)->where('status', 'pending')->count(),
            ],
            'leases' => [
                'active' => Lease::when($companyId, fn($q) => $q->whereHas('subnet', fn($s) => $s->where('company_id', $companyId)))->where('status', 'active')->count(),
                'expiring_soon' => Lease::when($companyId, fn($q) => $q->whereHas('subnet', fn($s) => $s->where('company_id', $companyId)))->where('status', 'active')->where('end_date', '<=', now()->addDays(30))->count(),
            ],
            'revenue' => $this->getRevenueStats($companyId),
            'ips' => $this->getIpStats($companyId),
        ];
    }

    /**
     * Get revenue statistics
     */
    public function getRevenueStats(?int $companyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $query = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate]);

        if ($companyId) {
            $query->whereHas('lease.subnet', fn($q) => $q->where('company_id', $companyId));
        }

        $revenue = $query->sum('total_amount');
        $count = $query->count();

        // Previous period for comparison
        $prevStart = $startDate->copy()->subMonth();
        $prevEnd = $endDate->copy()->subMonth();

        $prevQuery = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$prevStart, $prevEnd]);

        if ($companyId) {
            $prevQuery->whereHas('lease.subnet', fn($q) => $q->where('company_id', $companyId));
        }

        $prevRevenue = $prevQuery->sum('total_amount');

        $change = $prevRevenue > 0 ? (($revenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        return [
            'current' => $revenue,
            'previous' => $prevRevenue,
            'change_percent' => round($change, 2),
            'invoice_count' => $count,
            'currency' => 'EUR',
        ];
    }

    /**
     * Get IP statistics
     */
    public function getIpStats(?int $companyId = null): array
    {
        $query = fn() => $companyId ? Subnet::where('company_id', $companyId) : Subnet::query();

        $subnets = $query()->get();

        $totalIps = 0;
        $leasedIps = 0;
        $availableIps = 0;

        foreach ($subnets as $subnet) {
            $ips = pow(2, 32 - $subnet->cidr);
            $totalIps += $ips;

            if ($subnet->status === 'leased') {
                $leasedIps += $ips;
            } elseif ($subnet->status === 'available') {
                $availableIps += $ips;
            }
        }

        return [
            'total' => $totalIps,
            'leased' => $leasedIps,
            'available' => $availableIps,
            'utilization' => $totalIps > 0 ? round(($leasedIps / $totalIps) * 100, 2) : 0,
        ];
    }

    /**
     * Get revenue chart data
     */
    public function getRevenueChartData(?int $companyId = null, int $months = 12): array
    {
        $data = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $query = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month);

            if ($companyId) {
                $query->whereHas('lease.subnet', fn($q) => $q->where('company_id', $companyId));
            }

            $data[] = (float) $query->sum('total_amount');
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data),
            'average' => count($data) > 0 ? array_sum($data) / count($data) : 0,
        ];
    }

    /**
     * Get lease trend data
     */
    public function getLeaseTrendData(?int $companyId = null, int $months = 12): array
    {
        $created = [];
        $expired = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $createdQuery = Lease::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            $expiredQuery = Lease::whereYear('end_date', $date->year)
                ->whereMonth('end_date', $date->month)
                ->where('status', 'expired');

            if ($companyId) {
                $createdQuery->whereHas('subnet', fn($q) => $q->where('company_id', $companyId));
                $expiredQuery->whereHas('subnet', fn($q) => $q->where('company_id', $companyId));
            }

            $created[] = $createdQuery->count();
            $expired[] = $expiredQuery->count();
        }

        return [
            'labels' => $labels,
            'created' => $created,
            'expired' => $expired,
        ];
    }

    /**
     * Get subnet distribution by prefix
     */
    public function getSubnetDistribution(?int $companyId = null): array
    {
        $query = Subnet::select('cidr', DB::raw('count(*) as count'))
            ->groupBy('cidr')
            ->orderBy('cidr');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $result = $query->get();

        return [
            'labels' => $result->pluck('cidr')->map(fn($c) => '/' . $c)->toArray(),
            'data' => $result->pluck('count')->toArray(),
        ];
    }

    /**
     * Get top customers by revenue
     */
    public function getTopCustomers(int $limit = 10, ?Carbon $startDate = null): array
    {
        $startDate = $startDate ?? now()->startOfYear();

        return Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->select('user_id', DB::raw('SUM(total_amount) as total_revenue'), DB::raw('COUNT(*) as invoice_count'))
            ->groupBy('user_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->with('user:id,name,email')
            ->get()
            ->map(fn($row) => [
                'user' => $row->user?->name ?? 'Unknown',
                'email' => $row->user?->email ?? '',
                'revenue' => $row->total_revenue,
                'invoices' => $row->invoice_count,
            ])
            ->toArray();
    }

    /**
     * Get payout statistics
     */
    public function getPayoutStats(?int $companyId = null): array
    {
        $query = Payout::query();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $pending = (clone $query)->where('status', 'pending')->sum('amount');
        $processing = (clone $query)->where('status', 'processing')->sum('amount');
        $completed = (clone $query)->where('status', 'completed')->sum('amount');
        $thisMonth = (clone $query)->where('status', 'completed')
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->sum('amount');

        return [
            'pending' => $pending,
            'processing' => $processing,
            'completed_total' => $completed,
            'this_month' => $thisMonth,
        ];
    }

    /**
     * Get KYC statistics (admin only)
     */
    public function getKycStats(): array
    {
        return [
            'pending' => Company::where('kyc_status', 'pending')->count(),
            'in_review' => Company::where('kyc_status', 'in_review')->count(),
            'approved' => Company::where('kyc_status', 'approved')->count(),
            'rejected' => Company::where('kyc_status', 'rejected')->count(),
        ];
    }

    /**
     * Get user growth data
     */
    public function getUserGrowthData(int $months = 12): array
    {
        $data = [];
        $labels = [];
        $cumulative = [];
        $total = 0;

        // Get total users before the period
        $total = User::where('created_at', '<', now()->subMonths($months)->startOfMonth())->count();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = $count;
            $total += $count;
            $cumulative[] = $total;
        }

        return [
            'labels' => $labels,
            'new_users' => $data,
            'cumulative' => $cumulative,
        ];
    }

    /**
     * Get geographic distribution of subnets
     */
    public function getGeographicDistribution(?int $companyId = null): array
    {
        $query = Subnet::select('country', DB::raw('count(*) as count'), DB::raw('SUM(POWER(2, 32 - cidr)) as total_ips'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('count');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(fn($row) => [
            'country' => $row->country,
            'subnets' => $row->count,
            'ips' => (int) $row->total_ips,
        ])->toArray();
    }

    /**
     * Get IP reputation summary
     */
    public function getReputationSummary(?int $companyId = null): array
    {
        $query = Subnet::query();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $total = $query->count();
        $clean = (clone $query)->where('reputation_score', '>=', 80)->count();
        $warning = (clone $query)->whereBetween('reputation_score', [50, 79])->count();
        $critical = (clone $query)->where('reputation_score', '<', 50)->count();
        $unchecked = (clone $query)->whereNull('reputation_score')->count();

        return [
            'total' => $total,
            'clean' => $clean,
            'warning' => $warning,
            'critical' => $critical,
            'unchecked' => $unchecked,
            'clean_percent' => $total > 0 ? round(($clean / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Export analytics data
     */
    public function exportData(string $type, ?int $companyId = null, array $options = []): array
    {
        return match ($type) {
            'revenue' => $this->exportRevenueData($companyId, $options),
            'leases' => $this->exportLeaseData($companyId, $options),
            'subnets' => $this->exportSubnetData($companyId, $options),
            'customers' => $this->exportCustomerData($options),
            default => [],
        };
    }

    protected function exportRevenueData(?int $companyId, array $options): array
    {
        $startDate = isset($options['start_date']) ? Carbon::parse($options['start_date']) : now()->startOfYear();
        $endDate = isset($options['end_date']) ? Carbon::parse($options['end_date']) : now();

        $query = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with(['user', 'lease.subnet']);

        if ($companyId) {
            $query->whereHas('lease.subnet', fn($q) => $q->where('company_id', $companyId));
        }

        return $query->get()->map(fn($invoice) => [
            'invoice_number' => $invoice->invoice_number,
            'customer' => $invoice->user?->name,
            'subnet' => $invoice->lease?->subnet?->cidr_notation,
            'amount' => $invoice->total_amount,
            'currency' => $invoice->currency ?? 'EUR',
            'paid_at' => $invoice->paid_at?->format('Y-m-d H:i:s'),
        ])->toArray();
    }

    protected function exportLeaseData(?int $companyId, array $options): array
    {
        $query = Lease::with(['user', 'subnet']);

        if ($companyId) {
            $query->whereHas('subnet', fn($q) => $q->where('company_id', $companyId));
        }

        if (isset($options['status'])) {
            $query->where('status', $options['status']);
        }

        return $query->get()->map(fn($lease) => [
            'id' => $lease->id,
            'customer' => $lease->user?->name,
            'subnet' => $lease->subnet?->cidr_notation,
            'status' => $lease->status,
            'start_date' => $lease->start_date?->format('Y-m-d'),
            'end_date' => $lease->end_date?->format('Y-m-d'),
            'monthly_price' => $lease->monthly_price,
        ])->toArray();
    }

    protected function exportSubnetData(?int $companyId, array $options): array
    {
        $query = Subnet::with(['company']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(fn($subnet) => [
            'cidr' => $subnet->cidr_notation,
            'status' => $subnet->status,
            'rir' => $subnet->rir,
            'country' => $subnet->country,
            'reputation_score' => $subnet->reputation_score,
            'monthly_price' => $subnet->price_per_month,
            'owner' => $subnet->company?->company_name,
        ])->toArray();
    }

    protected function exportCustomerData(array $options): array
    {
        return User::with(['company'])
            ->withCount(['leases' => fn($q) => $q->where('status', 'active')])
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'email' => $user->email,
                'company' => $user->company?->company_name,
                'active_leases' => $user->leases_count,
                'created_at' => $user->created_at?->format('Y-m-d'),
            ])->toArray();
    }
}
