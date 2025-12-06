<?php

namespace App\Http\Controllers;

use App\Models\Subnet;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        $data = [
            'user' => $user,
            'company' => $company,
            'stats' => [],
            'recentLeases' => collect(),
            'recentInvoices' => collect(),
            'availableSubnets' => Subnet::available()->clean()->limit(5)->get(),
        ];

        if ($company) {
            // Stats for Lessees
            if ($company->isLessee()) {
                $data['stats']['activeLeases'] = Lease::forLessee($company->id)->active()->count();
                $data['stats']['totalIpsLeased'] = Lease::forLessee($company->id)
                    ->active()
                    ->with('subnet')
                    ->get()
                    ->sum(fn($lease) => $lease->subnet->ip_count);
                $data['stats']['pendingInvoices'] = Invoice::where('company_id', $company->id)
                    ->pending()
                    ->count();
                $data['recentLeases'] = Lease::forLessee($company->id)
                    ->with(['subnet', 'holderCompany'])
                    ->latest()
                    ->limit(5)
                    ->get();
            }

            // Stats for Holders
            if ($company->isHolder()) {
                $data['stats']['totalSubnets'] = Subnet::where('company_id', $company->id)->count();
                $data['stats']['availableSubnets'] = Subnet::where('company_id', $company->id)
                    ->available()
                    ->count();
                $data['stats']['leasedSubnets'] = Subnet::where('company_id', $company->id)
                    ->where('status', 'leased')
                    ->count();
                $data['stats']['totalEarnings'] = Payout::where('company_id', $company->id)
                    ->where('status', 'completed')
                    ->sum('net_amount');
                $data['stats']['pendingPayouts'] = Payout::where('company_id', $company->id)
                    ->pending()
                    ->sum('net_amount');
            }

            $data['recentInvoices'] = Invoice::where('company_id', $company->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('dashboard.index', $data);
    }
}
