<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Invoice;
use Illuminate\Http\Request;

class LeaseManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Lease::with(['subnet', 'lesseeCompany', 'holderCompany'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by expiring soon
        if ($request->has('expiring') && $request->expiring === 'true') {
            $query->where('end_date', '<=', now()->addDays(30))
                  ->where('status', 'active');
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('subnet', function ($q) use ($search) {
                    $q->where('ip_address', 'like', "%{$search}%");
                })
                ->orWhereHas('lesseeCompany', function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%");
                })
                ->orWhereHas('holderCompany', function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%");
                });
            });
        }

        $leases = $query->paginate(20);

        $stats = [
            'active' => Lease::where('status', 'active')->count(),
            'pending' => Lease::whereIn('status', ['pending_payment', 'pending_assignment'])->count(),
            'expiring_soon' => Lease::where('status', 'active')
                ->where('end_date', '<=', now()->addDays(30))
                ->count(),
            'monthly_revenue' => Lease::where('status', 'active')->sum('monthly_price'),
        ];

        return view('admin.leases.index', compact('leases', 'stats'));
    }

    public function show(Lease $lease)
    {
        $lease->load(['subnet.company', 'lesseeCompany.user', 'holderCompany.user', 'invoices.payments', 'loa', 'abuseReports']);

        return view('admin.leases.show', compact('lease'));
    }

    public function terminate(Request $request, Lease $lease)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $lease->update([
            'status' => 'terminated',
            'termination_reason' => $request->reason,
            'end_date' => now(),
        ]);

        // Update subnet status
        $lease->subnet->update(['status' => 'available']);

        return back()->with('success', "Lease terminated successfully.");
    }

    public function extend(Request $request, Lease $lease)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:12',
        ]);

        $newEndDate = $lease->end_date->addMonths($request->months);

        $lease->update([
            'end_date' => $newEndDate,
        ]);

        return back()->with('success', "Lease extended by {$request->months} months. New end date: {$newEndDate->format('Y-m-d')}");
    }
}
