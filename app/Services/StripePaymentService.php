<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe customer for user
     */
    public function createCustomer(User $user): string
    {
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
                'company' => $user->company?->company_name,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    /**
     * Create a checkout session for invoice payment
     */
    public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl): array
    {
        $user = $invoice->user;
        $customerId = $this->createCustomer($user);

        $session = $this->stripe->checkout->sessions->create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($invoice->currency ?? 'eur'),
                    'product_data' => [
                        'name' => "Invoice #{$invoice->invoice_number}",
                        'description' => $invoice->description ?? 'IP Lease Payment',
                    ],
                    'unit_amount' => (int) ($invoice->total_amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        return [
            'session_id' => $session->id,
            'url' => $session->url,
        ];
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(Invoice $invoice): array
    {
        $user = $invoice->user;
        $customerId = $this->createCustomer($user);

        $intent = $this->stripe->paymentIntents->create([
            'amount' => (int) ($invoice->total_amount * 100),
            'currency' => strtolower($invoice->currency ?? 'eur'),
            'customer' => $customerId,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        return [
            'client_secret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ];
    }

    /**
     * Confirm payment from webhook
     */
    public function confirmPayment(string $paymentIntentId): ?Payment
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            if ($intent->status !== 'succeeded') {
                return null;
            }

            $invoiceId = $intent->metadata->invoice_id ?? null;

            if (!$invoiceId) {
                return null;
            }

            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                return null;
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'amount' => $intent->amount / 100,
                'currency' => strtoupper($intent->currency),
                'payment_method' => 'stripe',
                'transaction_id' => $intent->id,
                'status' => 'completed',
                'paid_at' => now(),
                'metadata' => [
                    'stripe_payment_intent' => $intent->id,
                    'stripe_customer' => $intent->customer,
                ],
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $payment;
        } catch (\Exception $e) {
            Log::error('Stripe payment confirmation failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a subscription for recurring payments
     */
    public function createSubscription(User $user, string $priceId): array
    {
        $customerId = $this->createCustomer($user);

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return [
            'subscription_id' => $subscription->id,
            'client_secret' => $subscription->latest_invoice->payment_intent->client_secret,
        ];
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $this->stripe->subscriptions->cancel($subscriptionId);
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe subscription cancellation failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Add payment method to customer
     */
    public function addPaymentMethod(User $user, string $paymentMethodId): PaymentMethod
    {
        $customerId = $this->createCustomer($user);

        $this->stripe->paymentMethods->attach($paymentMethodId, [
            'customer' => $customerId,
        ]);

        $pm = $this->stripe->paymentMethods->retrieve($paymentMethodId);

        return PaymentMethod::create([
            'user_id' => $user->id,
            'stripe_payment_method_id' => $paymentMethodId,
            'type' => $pm->type,
            'card_brand' => $pm->card?->brand,
            'card_last_four' => $pm->card?->last4,
            'card_exp_month' => $pm->card?->exp_month,
            'card_exp_year' => $pm->card?->exp_year,
            'is_default' => PaymentMethod::where('user_id', $user->id)->count() === 0,
        ]);
    }

    /**
     * Remove payment method
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        try {
            $this->stripe->paymentMethods->detach($paymentMethod->stripe_payment_method_id);
            $paymentMethod->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove payment method', [
                'payment_method_id' => $paymentMethod->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Charge customer with saved payment method
     */
    public function chargeCustomer(User $user, float $amount, string $currency, string $description, ?PaymentMethod $paymentMethod = null): ?Payment
    {
        try {
            $customerId = $this->createCustomer($user);

            $pm = $paymentMethod ?? PaymentMethod::where('user_id', $user->id)->where('is_default', true)->first();

            if (!$pm) {
                throw new \Exception('No payment method available');
            }

            $intent = $this->stripe->paymentIntents->create([
                'amount' => (int) ($amount * 100),
                'currency' => strtolower($currency),
                'customer' => $customerId,
                'payment_method' => $pm->stripe_payment_method_id,
                'off_session' => true,
                'confirm' => true,
                'description' => $description,
            ]);

            if ($intent->status === 'succeeded') {
                return Payment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency' => strtoupper($currency),
                    'payment_method' => 'stripe',
                    'transaction_id' => $intent->id,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Stripe charge failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Process refund
     */
    public function refund(Payment $payment, ?float $amount = null): bool
    {
        try {
            $this->stripe->refunds->create([
                'payment_intent' => $payment->transaction_id,
                'amount' => $amount ? (int) ($amount * 100) : null,
            ]);

            $payment->update([
                'status' => $amount ? 'partially_refunded' : 'refunded',
                'refunded_amount' => ($payment->refunded_amount ?? 0) + ($amount ?? $payment->amount),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Stripe refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            return match ($event->type) {
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
                'customer.subscription.deleted' => $this->handleSubscriptionCanceled($event->data->object),
                'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event->data->object),
                default => ['status' => 'ignored', 'type' => $event->type],
            };
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function handlePaymentSucceeded($paymentIntent): array
    {
        $payment = $this->confirmPayment($paymentIntent->id);
        return ['status' => 'success', 'payment_id' => $payment?->id];
    }

    protected function handlePaymentFailed($paymentIntent): array
    {
        Log::warning('Payment failed', ['payment_intent' => $paymentIntent->id]);
        return ['status' => 'failed', 'payment_intent' => $paymentIntent->id];
    }

    protected function handleSubscriptionCanceled($subscription): array
    {
        Log::info('Subscription canceled', ['subscription_id' => $subscription->id]);
        return ['status' => 'subscription_canceled', 'subscription_id' => $subscription->id];
    }

    protected function handleInvoicePaymentFailed($invoice): array
    {
        Log::warning('Invoice payment failed', ['stripe_invoice' => $invoice->id]);
        return ['status' => 'invoice_failed', 'stripe_invoice' => $invoice->id];
    }
}
