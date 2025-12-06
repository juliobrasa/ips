<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use Illuminate\Http\Request;

class LeaseController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('company.create')
                ->with('error', 'Please create a company profile first.');
        }

        $leasesAsLessee = collect();
        $leasesAsHolder = collect();

        if ($company->isLessee()) {
            $leasesAsLessee = Lease::forLessee($company->id)
                ->with(['subnet', 'holderCompany'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'lessee_page');
        }

        if ($company->isHolder()) {
            $leasesAsHolder = Lease::forHolder($company->id)
                ->with(['subnet', 'lesseeCompany'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'holder_page');
        }

        return view('leases.index', compact('leasesAsLessee', 'leasesAsHolder', 'company'));
    }

    public function show(Lease $lease)
    {
        $company = auth()->user()->company;

        if (!$company || ($lease->lessee_company_id !== $company->id && $lease->holder_company_id !== $company->id)) {
            abort(403);
        }

        $lease->load(['subnet', 'lesseeCompany', 'holderCompany', 'invoices', 'loa']);

        $isLessee = $lease->lessee_company_id === $company->id;

        return view('leases.show', compact('lease', 'isLessee'));
    }

    public function assignAsn(Request $request, Lease $lease)
    {
        $company = auth()->user()->company;

        if (!$company || $lease->lessee_company_id !== $company->id) {
            abort(403);
        }

        if (!$lease->needsAsn()) {
            return back()->with('error', 'ASN has already been assigned or lease is not ready.');
        }

        $validated = $request->validate([
            'asn' => [
                'required',
                'string',
                'regex:/^AS\d{1,10}$/i',
            ],
        ]);

        $asn = strtoupper($validated['asn']);

        $lease->update([
            'assigned_asn' => $asn,
            'status' => 'active',
        ]);

        return redirect()->route('loa.generate', $lease)
            ->with('success', "ASN {$asn} assigned. Generating LOA...");
    }

    public function renew(Request $request, Lease $lease)
    {
        $company = auth()->user()->company;

        if (!$company || $lease->lessee_company_id !== $company->id) {
            abort(403);
        }

        if (!$lease->isActive()) {
            return back()->with('error', 'Only active leases can be renewed.');
        }

        $validated = $request->validate([
            'months' => 'required|integer|min:1|max:36',
        ]);

        $lease->update([
            'end_date' => $lease->end_date->addMonths($validated['months']),
        ]);

        // Create renewal invoice
        // ... (simplified for demo)

        return back()->with('success', "Lease renewed for {$validated['months']} months.");
    }

    public function terminate(Request $request, Lease $lease)
    {
        $company = auth()->user()->company;

        if (!$company || ($lease->lessee_company_id !== $company->id && $lease->holder_company_id !== $company->id)) {
            abort(403);
        }

        if (!$lease->isActive()) {
            return back()->with('error', 'Only active leases can be terminated.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $lease->update([
            'status' => 'terminated',
            'termination_reason' => $validated['reason'],
            'end_date' => now(),
        ]);

        // Release subnet
        $lease->subnet->update(['status' => 'available']);

        // Revoke LOA
        if ($lease->loa) {
            $lease->loa->revoke();
        }

        return redirect()->route('leases.index')
            ->with('success', 'Lease terminated successfully.');
    }
}
