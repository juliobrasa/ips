<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company || !$company->isHolder()) {
            return redirect()->route('dashboard')
                ->with('error', 'Payouts are only available for IP Holders.');
        }

        $payouts = Payout::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'pending' => Payout::where('company_id', $company->id)->where('status', 'pending')->sum('net_amount'),
            'completed' => Payout::where('company_id', $company->id)->where('status', 'completed')->sum('net_amount'),
            'thisMonth' => Payout::where('company_id', $company->id)
                ->where('status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('net_amount'),
        ];

        return view('payouts.index', compact('payouts', 'stats'));
    }

    public function show(Payout $payout)
    {
        $company = auth()->user()->company;

        if (!$company || $payout->company_id !== $company->id) {
            abort(403);
        }

        return view('payouts.show', compact('payout'));
    }
}
