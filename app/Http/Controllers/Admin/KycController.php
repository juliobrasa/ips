<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('kyc_status', $request->status);
        }

        // Filter by company type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('company_type', $request->type);
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('legal_name', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $companies = $query->paginate(20);

        return view('admin.kyc.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['user', 'subnets', 'leasesAsHolder', 'leasesAsLessee', 'invoices', 'payouts']);

        return view('admin.kyc.show', compact('company'));
    }

    public function review(Company $company)
    {
        if ($company->kyc_status === 'pending') {
            $company->update(['kyc_status' => 'in_review']);
        }

        return view('admin.kyc.review', compact('company'));
    }

    public function approve(Request $request, Company $company)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $company->update([
            'kyc_status' => 'approved',
            'kyc_approved_at' => now(),
            'kyc_notes' => $request->notes,
        ]);

        // Send approval email
        $this->sendKycStatusEmail($company, 'approved');

        return redirect()->route('admin.kyc.index')
            ->with('success', "KYC approved for {$company->company_name}");
    }

    public function reject(Request $request, Company $company)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $company->update([
            'kyc_status' => 'rejected',
            'kyc_notes' => $request->notes,
        ]);

        // Send rejection email
        $this->sendKycStatusEmail($company, 'rejected');

        return redirect()->route('admin.kyc.index')
            ->with('success', "KYC rejected for {$company->company_name}");
    }

    public function requestInfo(Request $request, Company $company)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $company->update([
            'kyc_status' => 'pending',
            'kyc_notes' => $request->notes,
        ]);

        // Send info request email
        $this->sendKycStatusEmail($company, 'info_requested');

        return redirect()->route('admin.kyc.index')
            ->with('success', "Additional information requested from {$company->company_name}");
    }

    protected function sendKycStatusEmail(Company $company, string $status)
    {
        try {
            Mail::send("emails.kyc-{$status}", [
                'company' => $company,
                'user' => $company->user,
            ], function ($message) use ($company, $status) {
                $subject = match ($status) {
                    'approved' => 'KYC Approved - Soltia IPS Marketplace',
                    'rejected' => 'KYC Verification Failed - Soltia IPS Marketplace',
                    'info_requested' => 'Additional Information Required - Soltia IPS Marketplace',
                    default => 'KYC Status Update - Soltia IPS Marketplace',
                };

                $message->to($company->user->email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send KYC email: " . $e->getMessage());
        }
    }
}
