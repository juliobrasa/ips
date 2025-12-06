<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function invoices(Request $request)
    {
        $query = Invoice::with(['company', 'lease.subnet'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from') && !empty($request->from)) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to') && !empty($request->to)) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $invoices = $query->paginate(20);

        $stats = [
            'total_invoiced' => Invoice::sum('total'),
            'paid' => Invoice::where('status', 'paid')->sum('total'),
            'pending' => Invoice::where('status', 'pending')->sum('total'),
            'overdue' => Invoice::where('status', 'overdue')->sum('total'),
        ];

        return view('admin.finance.invoices', compact('invoices', 'stats'));
    }

    public function payouts(Request $request)
    {
        $query = Payout::with(['company'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $payouts = $query->paginate(20);

        $stats = [
            'total_paid' => Payout::where('status', 'paid')->sum('net_amount'),
            'pending' => Payout::where('status', 'pending')->sum('net_amount'),
            'processing' => Payout::where('status', 'processing')->sum('net_amount'),
        ];

        return view('admin.finance.payouts', compact('payouts', 'stats'));
    }

    public function processPayout(Payout $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', 'Payout is not pending.');
        }

        $payout->update([
            'status' => 'processing',
        ]);

        return back()->with('success', "Payout marked as processing. Amount: \${$payout->net_amount}");
    }

    public function completePayout(Request $request, Payout $payout)
    {
        $request->validate([
            'transaction_id' => 'required|string|max:100',
        ]);

        if ($payout->status !== 'processing') {
            return back()->with('error', 'Payout must be in processing status.');
        }

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $request->transaction_id,
        ]);

        return back()->with('success', "Payout completed. Transaction ID: {$request->transaction_id}");
    }

    public function revenueReport(Request $request)
    {
        $period = $request->get('period', 'month');

        $groupBy = match ($period) {
            'day' => DB::raw('DATE(created_at)'),
            'week' => DB::raw('YEARWEEK(created_at)'),
            'month' => DB::raw('DATE_FORMAT(created_at, "%Y-%m")'),
            'year' => DB::raw('YEAR(created_at)'),
            default => DB::raw('DATE_FORMAT(created_at, "%Y-%m")'),
        };

        $revenue = Invoice::where('status', 'paid')
            ->select([
                $groupBy->getValue(DB::connection()->getQueryGrammar()) . ' as period',
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(tax) as tax'),
                DB::raw('COUNT(*) as invoice_count'),
            ])
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(24)
            ->get();

        $holderPayouts = Payout::where('status', 'paid')
            ->select([
                $groupBy->getValue(DB::connection()->getQueryGrammar()) . ' as period',
                DB::raw('SUM(net_amount) as payouts'),
            ])
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(24)
            ->get()
            ->keyBy('period');

        // Calculate platform earnings
        $data = $revenue->map(function ($item) use ($holderPayouts) {
            $payouts = $holderPayouts[$item->period]->payouts ?? 0;
            return [
                'period' => $item->period,
                'revenue' => $item->revenue,
                'tax' => $item->tax,
                'payouts' => $payouts,
                'platform_earnings' => $item->revenue - $payouts,
                'invoice_count' => $item->invoice_count,
            ];
        });

        return view('admin.finance.revenue', compact('data', 'period'));
    }
}
