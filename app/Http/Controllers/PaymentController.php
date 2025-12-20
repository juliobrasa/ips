<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(protected StripePaymentService $stripe)
    {
    }

    /**
     * Show payment methods
     */
    public function methods(): View
    {
        $paymentMethods = PaymentMethod::where('user_id', auth()->id())->get();

        return view('payments.methods', [
            'paymentMethods' => $paymentMethods,
            'stripePublicKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Add payment method
     */
    public function addMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        try {
            $paymentMethod = $this->stripe->addPaymentMethod(
                auth()->user(),
                $request->payment_method_id
            );

            return response()->json([
                'success' => true,
                'payment_method' => [
                    'id' => $paymentMethod->id,
                    'display_name' => $paymentMethod->display_name,
                    'expiration' => $paymentMethod->expiration,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove payment method
     */
    public function removeMethod(PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->user_id !== auth()->id()) {
            abort(403);
        }

        $this->stripe->removePaymentMethod($paymentMethod);

        return back()->with('success', __('Payment method removed successfully.'));
    }

    /**
     * Set default payment method
     */
    public function setDefault(PaymentMethod $method): RedirectResponse
    {
        if ($method->user_id !== auth()->id()) {
            abort(403);
        }

        // Remove default from all
        PaymentMethod::where('user_id', auth()->id())->update(['is_default' => false]);

        // Set this as default
        $method->update(['is_default' => true]);

        return back()->with('success', __('Default payment method updated.'));
    }

    /**
     * Create checkout session for invoice
     */
    public function checkout(Invoice $invoice): JsonResponse
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return response()->json([
                'error' => 'Invoice is already paid',
            ], 400);
        }

        try {
            $session = $this->stripe->createCheckoutSession(
                $invoice,
                route('invoices.show', $invoice) . '?payment=success',
                route('invoices.show', $invoice) . '?payment=cancelled'
            );

            return response()->json($session);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $result = $this->stripe->handleWebhook($payload, $signature);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get payment intent for pay with saved card
     */
    public function payWithSavedCard(Invoice $invoice): JsonResponse
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $intent = $this->stripe->createPaymentIntent($invoice);
            return response()->json($intent);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
