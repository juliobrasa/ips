<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('company.create')
                ->with('error', 'Please create a company profile first.');
        }

        $invoices = Invoice::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'pending' => Invoice::where('company_id', $company->id)->pending()->sum('total'),
            'overdue' => Invoice::where('company_id', $company->id)->overdue()->sum('total'),
            'paid' => Invoice::where('company_id', $company->id)->paid()->sum('total'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        $company = auth()->user()->company;

        if (!$company || $invoice->company_id !== $company->id) {
            abort(403);
        }

        $invoice->load(['payments', 'lease.subnet']);

        return view('invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $company = auth()->user()->company;

        if (!$company || $invoice->company_id !== $company->id) {
            abort(403);
        }

        // In a real implementation, generate PDF
        // For demo, return a simple response

        return response()->json([
            'message' => 'PDF generation not implemented in demo',
            'invoice' => $invoice,
        ]);
    }

    public function pay(Request $request, Invoice $invoice)
    {
        $company = auth()->user()->company;

        if (!$company || $invoice->company_id !== $company->id) {
            abort(403);
        }

        if ($invoice->isPaid()) {
            return back()->with('info', 'This invoice has already been paid.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,paypal,balance',
        ]);

        // In a real implementation, integrate with payment gateway
        // For demo, we'll simulate a successful payment

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_method' => $validated['payment_method'],
            'amount' => $invoice->total,
            'currency' => $invoice->currency,
            'status' => 'completed',
            'gateway_transaction_id' => 'DEMO-' . strtoupper(uniqid()),
        ]);

        $invoice->markAsPaid();

        // Activate related leases
        if ($invoice->lease) {
            $invoice->lease->update(['status' => 'pending_assignment']);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment successful! Your lease is now pending ASN assignment.');
    }
}
