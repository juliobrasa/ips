<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Subnet;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payout;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'pending_kyc' => Company::where('kyc_status', 'pending')->count(),
            'in_review_kyc' => Company::where('kyc_status', 'in_review')->count(),
            'total_subnets' => Subnet::count(),
            'available_subnets' => Subnet::where('status', 'available')->count(),
            'pending_subnets' => Subnet::where('status', 'pending_verification')->count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
            'pending_payouts' => Payout::where('status', 'pending')->sum('net_amount'),
        ];

        $recentKycRequests = Company::whereIn('kyc_status', ['pending', 'in_review'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentSubnets = Subnet::where('status', 'pending_verification')
            ->with('company')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentKycRequests', 'recentSubnets'));
    }
}
