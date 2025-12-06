<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class KycDocumentController extends Controller
{
    /**
     * Show the KYC documents page
     */
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('company.create')
                ->with('warning', __('Please complete your company profile first.'));
        }

        return view('profile.kyc-documents', compact('company'));
    }

    /**
     * Upload identity document
     */
    public function uploadIdentityDocument(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return back()->with('error', __('Please complete your company profile first.'));
        }

        $request->validate([
            'identity_document_type' => 'required|in:dni,nie,passport,nif,cif',
            'identity_document_number' => 'required|string|max:50',
            'identity_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Delete old file if exists
        if ($company->identity_document_file) {
            Storage::disk('public')->delete($company->identity_document_file);
        }

        // Store new file
        $path = $request->file('identity_document_file')->store(
            'kyc-documents/' . $company->id . '/identity',
            'public'
        );

        $company->update([
            'identity_document_type' => $request->identity_document_type,
            'identity_document_number' => $request->identity_document_number,
            'identity_document_file' => $path,
            'identity_document_uploaded_at' => now(),
            'kyc_status' => $company->kyc_status === 'rejected' ? 'pending' : $company->kyc_status,
        ]);

        return back()->with('success', __('Identity document uploaded successfully.'));
    }

    /**
     * Upload signed KYC document
     */
    public function uploadSignedKyc(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return back()->with('error', __('Please complete your company profile first.'));
        }

        $request->validate([
            'kyc_signed_document' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Delete old file if exists
        if ($company->kyc_signed_document) {
            Storage::disk('public')->delete($company->kyc_signed_document);
        }

        // Store new file
        $path = $request->file('kyc_signed_document')->store(
            'kyc-documents/' . $company->id . '/signed',
            'public'
        );

        $company->update([
            'kyc_signed_document' => $path,
            'kyc_signed_uploaded_at' => now(),
            'kyc_status' => $company->kyc_status === 'rejected' ? 'pending' : $company->kyc_status,
        ]);

        // If all documents are uploaded, change status to in_review
        if ($company->hasAllDocuments() && $company->kyc_status === 'pending') {
            $company->update(['kyc_status' => 'in_review']);
        }

        return back()->with('success', __('Signed KYC document uploaded successfully.'));
    }

    /**
     * Download KYC form pre-filled with user data
     */
    public function downloadKycForm()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return back()->with('error', __('Please complete your company profile first.'));
        }

        $user = auth()->user();

        $pdf = Pdf::loadView('pdf.kyc-form', compact('company', 'user'));

        return $pdf->download('kyc-form-' . $company->id . '.pdf');
    }

    /**
     * View KYC form (for printing)
     */
    public function viewKycForm()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return back()->with('error', __('Please complete your company profile first.'));
        }

        $user = auth()->user();

        return view('pdf.kyc-form', compact('company', 'user'));
    }

    /**
     * Submit KYC for review
     */
    public function submitForReview()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return back()->with('error', __('Please complete your company profile first.'));
        }

        if (!$company->hasAllDocuments()) {
            return back()->with('error', __('Please upload all required documents before submitting for review.'));
        }

        if ($company->kyc_status === 'in_review') {
            return back()->with('info', __('Your KYC is already under review.'));
        }

        if ($company->kyc_status === 'approved') {
            return back()->with('info', __('Your KYC is already approved.'));
        }

        $company->update(['kyc_status' => 'in_review']);

        return back()->with('success', __('Your KYC has been submitted for review. We will notify you once it is processed.'));
    }
}
