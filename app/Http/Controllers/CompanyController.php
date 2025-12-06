<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function create()
    {
        if (auth()->user()->company) {
            return redirect()->route('company.edit');
        }

        return view('company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'country' => 'required|string|size:2',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_type' => ['required', Rule::in(['holder', 'lessee', 'both'])],
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $kycDocuments = [];

        if ($request->hasFile('kyc_document')) {
            $path = $request->file('kyc_document')->store('kyc-documents', 'private');
            $kycDocuments['company_registration'] = $path;
        }

        if ($request->hasFile('id_document')) {
            $path = $request->file('id_document')->store('kyc-documents', 'private');
            $kycDocuments['id_document'] = $path;
        }

        $company = Company::create([
            'user_id' => auth()->id(),
            'company_name' => $validated['company_name'],
            'legal_name' => $validated['legal_name'],
            'tax_id' => $validated['tax_id'],
            'country' => strtoupper($validated['country']),
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'],
            'company_type' => $validated['company_type'],
            'kyc_status' => empty($kycDocuments) ? 'pending' : 'in_review',
            'kyc_documents' => $kycDocuments,
        ]);

        // Activate user
        auth()->user()->update(['status' => 'active']);

        return redirect()->route('dashboard')
            ->with('success', 'Company profile created successfully. ' .
                (empty($kycDocuments) ? 'Please upload KYC documents to complete verification.' : 'Your documents are under review.'));
    }

    public function edit()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('company.create');
        }

        return view('company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('company.create');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'country' => 'required|string|size:2',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_type' => ['required', Rule::in(['holder', 'lessee', 'both'])],
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'payout_method' => 'nullable|string|in:paypal,bank_transfer,wise',
            'payout_email' => 'nullable|email|required_if:payout_method,paypal,wise',
            'bank_name' => 'nullable|string|required_if:payout_method,bank_transfer',
            'bank_account' => 'nullable|string|required_if:payout_method,bank_transfer',
            'bank_swift' => 'nullable|string|required_if:payout_method,bank_transfer',
        ]);

        $kycDocuments = $company->kyc_documents ?? [];
        $kycChanged = false;

        if ($request->hasFile('kyc_document')) {
            if (isset($kycDocuments['company_registration'])) {
                Storage::disk('private')->delete($kycDocuments['company_registration']);
            }
            $path = $request->file('kyc_document')->store('kyc-documents', 'private');
            $kycDocuments['company_registration'] = $path;
            $kycChanged = true;
        }

        if ($request->hasFile('id_document')) {
            if (isset($kycDocuments['id_document'])) {
                Storage::disk('private')->delete($kycDocuments['id_document']);
            }
            $path = $request->file('id_document')->store('kyc-documents', 'private');
            $kycDocuments['id_document'] = $path;
            $kycChanged = true;
        }

        // Payout details
        $payoutDetails = [];
        if ($validated['payout_method'] ?? null) {
            if ($validated['payout_method'] === 'paypal' || $validated['payout_method'] === 'wise') {
                $payoutDetails['email'] = $validated['payout_email'];
            } elseif ($validated['payout_method'] === 'bank_transfer') {
                $payoutDetails['bank_name'] = $validated['bank_name'];
                $payoutDetails['account_number'] = $validated['bank_account'];
                $payoutDetails['swift_code'] = $validated['bank_swift'];
            }
        }

        $company->update([
            'company_name' => $validated['company_name'],
            'legal_name' => $validated['legal_name'],
            'tax_id' => $validated['tax_id'],
            'country' => strtoupper($validated['country']),
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'],
            'company_type' => $validated['company_type'],
            'kyc_documents' => $kycDocuments,
            'kyc_status' => $kycChanged ? 'in_review' : $company->kyc_status,
            'payout_method' => $validated['payout_method'] ?? $company->payout_method,
            'payout_details' => !empty($payoutDetails) ? $payoutDetails : $company->payout_details,
        ]);

        return redirect()->route('company.edit')
            ->with('success', 'Company profile updated successfully.');
    }
}
