<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Loa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LoaController extends Controller
{
    public function generate(Lease $lease)
    {
        $company = auth()->user()->company;

        // Check authorization - lessee can generate, holder can view
        if (!$company || ($lease->lessee_company_id !== $company->id && $lease->holder_company_id !== $company->id)) {
            abort(403);
        }

        // Only lessee can generate new LOA
        if ($lease->lessee_company_id !== $company->id && !$lease->loa) {
            abort(403, 'Only the lessee can generate a new LOA.');
        }

        if (!$lease->assigned_asn) {
            return redirect()->route('leases.show', $lease)
                ->with('error', 'Please assign an ASN first before generating the LOA.');
        }

        // Check if LOA already exists
        if ($lease->loa) {
            return redirect()->route('loa.download', $lease->loa);
        }

        $subnet = $lease->subnet;

        // Generate unique LOA number
        $loaNumber = 'LOA-' . date('Y') . '-' . str_pad(Loa::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);

        // Create signature hash with multiple data points for verification
        $signatureData = implode('|', [
            $lease->id,
            $subnet->cidr_notation,
            $lease->assigned_asn,
            $lease->holderCompany->company_name,
            $lease->lesseeCompany->company_name,
            $lease->start_date->format('Y-m-d'),
            $lease->end_date->format('Y-m-d'),
            now()->timestamp,
        ]);
        $signatureHash = hash('sha256', $signatureData);

        $loa = Loa::create([
            'lease_id' => $lease->id,
            'loa_number' => $loaNumber,
            'ip_range' => $subnet->cidr_notation,
            'authorized_asn' => $lease->assigned_asn,
            'valid_from' => $lease->start_date,
            'valid_until' => $lease->end_date,
            'holder_company_name' => $lease->holderCompany->company_name,
            'lessee_company_name' => $lease->lesseeCompany->company_name,
            'signature_hash' => $signatureHash,
        ]);

        $lease->update(['loa_generated_at' => now()]);

        return redirect()->route('loa.download', $loa)
            ->with('success', 'LOA generated successfully. You can now download the PDF.');
    }

    public function download(Loa $loa)
    {
        $company = auth()->user()->company;
        $lease = $loa->lease;

        // Both holder and lessee can download
        if (!$company || ($lease->lessee_company_id !== $company->id && $lease->holder_company_id !== $company->id)) {
            // Also allow admin
            if (auth()->user()->role !== 'admin') {
                abort(403);
            }
        }

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('loa.pdf-template', [
            'loa' => $loa,
            'lease' => $lease,
        ]);

        // Set PDF options for better rendering
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'sans-serif');

        // Return PDF for download
        return $pdf->download("LOA-{$loa->loa_number}.pdf");
    }

    public function view(Loa $loa)
    {
        $company = auth()->user()->company;
        $lease = $loa->lease;

        if (!$company || ($lease->lessee_company_id !== $company->id && $lease->holder_company_id !== $company->id)) {
            if (auth()->user()->role !== 'admin') {
                abort(403);
            }
        }

        // Generate PDF for inline viewing
        $pdf = Pdf::loadView('loa.pdf-template', [
            'loa' => $loa,
            'lease' => $lease,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("LOA-{$loa->loa_number}.pdf");
    }

    public function verify(Request $request)
    {
        $request->validate([
            'loa_number' => 'required|string',
            'hash' => 'required|string|size:64',
        ]);

        $loa = Loa::where('loa_number', $request->loa_number)
            ->where('signature_hash', $request->hash)
            ->first();

        if (!$loa) {
            return response()->json([
                'valid' => false,
                'message' => 'LOA not found or signature does not match.',
            ]);
        }

        $isExpired = $loa->valid_until->isPast();

        return response()->json([
            'valid' => true,
            'expired' => $isExpired,
            'loa' => [
                'loa_number' => $loa->loa_number,
                'ip_range' => $loa->ip_range,
                'authorized_asn' => $loa->authorized_asn,
                'holder' => $loa->holder_company_name,
                'lessee' => $loa->lessee_company_name,
                'valid_from' => $loa->valid_from->format('Y-m-d'),
                'valid_until' => $loa->valid_until->format('Y-m-d'),
                'status' => $isExpired ? 'expired' : 'active',
            ],
            'message' => $isExpired
                ? 'This LOA is valid but has expired.'
                : 'This LOA is valid and currently active.',
        ]);
    }
}
