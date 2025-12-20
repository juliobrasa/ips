<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(protected AnalyticsService $analytics)
    {
    }

    /**
     * Show analytics dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        $companyId = $user->company?->id;

        $stats = $this->analytics->getDashboardStats($companyId);
        $revenueChart = $this->analytics->getRevenueChartData($companyId, 12);
        $leaseTrend = $this->analytics->getLeaseTrendData($companyId, 12);
        $subnetDistribution = $this->analytics->getSubnetDistribution($companyId);
        $reputationSummary = $this->analytics->getReputationSummary($companyId);

        return view('analytics.index', [
            'stats' => $stats,
            'revenueChart' => $revenueChart,
            'leaseTrend' => $leaseTrend,
            'subnetDistribution' => $subnetDistribution,
            'reputationSummary' => $reputationSummary,
        ]);
    }

    /**
     * Get revenue data (API)
     */
    public function revenueData(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company?->id;
        $months = $request->input('months', 12);

        return response()->json(
            $this->analytics->getRevenueChartData($companyId, $months)
        );
    }

    /**
     * Get lease trend data (API)
     */
    public function leaseTrendData(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company?->id;
        $months = $request->input('months', 12);

        return response()->json(
            $this->analytics->getLeaseTrendData($companyId, $months)
        );
    }

    /**
     * Export data
     */
    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'type' => 'required|in:revenue,leases,subnets,customers',
            'format' => 'required|in:csv,json',
        ]);

        $companyId = auth()->user()->company?->id;
        $type = $request->input('type');
        $format = $request->input('format');
        $options = $request->only(['start_date', 'end_date', 'status']);

        $data = $this->analytics->exportData($type, $companyId, $options);

        $filename = "export_{$type}_" . now()->format('Y-m-d_His');

        if ($format === 'json') {
            return response()->streamDownload(function () use ($data) {
                echo json_encode($data, JSON_PRETTY_PRINT);
            }, "{$filename}.json", [
                'Content-Type' => 'application/json',
            ]);
        }

        // CSV export
        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');

            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }

            fclose($output);
        }, "{$filename}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Get geographic distribution
     */
    public function geoDistribution(): JsonResponse
    {
        $companyId = auth()->user()->company?->id;

        return response()->json(
            $this->analytics->getGeographicDistribution($companyId)
        );
    }

    /**
     * Get payout stats
     */
    public function payoutStats(): JsonResponse
    {
        $companyId = auth()->user()->company?->id;

        return response()->json(
            $this->analytics->getPayoutStats($companyId)
        );
    }
}
