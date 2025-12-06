<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Subnet;
use App\Models\Lease;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('subnet.company')->get();

        $total = $cartItems->sum(function ($item) {
            return $item->subnet->total_monthly_price * $item->lease_months;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Subnet $subnet)
    {
        $company = auth()->user()->company;

        if (!$company || !$company->canLease()) {
            return redirect()->route('company.edit')
                ->with('error', 'Please complete your company profile and KYC to lease subnets.');
        }

        if (!$subnet->isAvailable()) {
            return back()->with('error', 'This subnet is no longer available.');
        }

        // Check if already in cart
        $existing = CartItem::where('user_id', auth()->id())
            ->where('subnet_id', $subnet->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'This subnet is already in your cart.');
        }

        $validated = $request->validate([
            'lease_months' => 'required|integer|min:' . $subnet->min_lease_months . '|max:36',
        ]);

        CartItem::create([
            'user_id' => auth()->id(),
            'subnet_id' => $subnet->id,
            'lease_months' => $validated['lease_months'],
            'reserved_until' => now()->addMinutes(15),
        ]);

        // Mark subnet as reserved
        $subnet->update(['status' => 'reserved']);

        return redirect()->route('cart.index')
            ->with('success', 'Subnet added to cart. You have 15 minutes to complete checkout.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'lease_months' => 'required|integer|min:' . $cartItem->subnet->min_lease_months . '|max:36',
        ]);

        $cartItem->update([
            'lease_months' => $validated['lease_months'],
        ]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        // Release subnet reservation
        $cartItem->subnet->update(['status' => 'available']);

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function checkout(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company || !$company->canLease()) {
            return redirect()->route('company.edit')
                ->with('error', 'Please complete your company profile and KYC to proceed.');
        }

        $cartItems = $user->cartItems()->with('subnet.company')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Verify all subnets are still reserved for this user
        foreach ($cartItems as $item) {
            if ($item->subnet->status !== 'reserved') {
                $item->delete();
                return redirect()->route('cart.index')
                    ->with('error', 'Some items in your cart are no longer available.');
            }
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $leases = [];

            foreach ($cartItems as $item) {
                $subnet = $item->subnet;
                $monthlyPrice = $subnet->total_monthly_price * 1.10; // 10% platform fee

                // Create lease
                $lease = Lease::create([
                    'subnet_id' => $subnet->id,
                    'lessee_company_id' => $company->id,
                    'holder_company_id' => $subnet->company_id,
                    'start_date' => now(),
                    'end_date' => now()->addMonths($item->lease_months),
                    'auto_renew' => true,
                    'monthly_price' => $monthlyPrice,
                    'platform_fee_percentage' => 10.00,
                    'status' => 'pending_payment',
                ]);

                $leases[] = $lease;
                $totalAmount += $monthlyPrice * $item->lease_months;

                // Update subnet status
                $subnet->update(['status' => 'leased']);
            }

            // Create invoice
            $invoice = Invoice::create([
                'company_id' => $company->id,
                'lease_id' => $leases[0]->id, // Link to first lease
                'type' => 'lease',
                'subtotal' => $totalAmount / 1.10,
                'tax' => 0,
                'total' => $totalAmount,
                'issue_date' => now(),
                'due_date' => now()->addDays(7),
                'line_items' => $cartItems->map(function ($item) {
                    return [
                        'description' => "IP Lease: {$item->subnet->cidr_notation}",
                        'months' => $item->lease_months,
                        'monthly_price' => $item->subnet->total_monthly_price * 1.10,
                        'total' => $item->subnet->total_monthly_price * 1.10 * $item->lease_months,
                    ];
                })->toArray(),
            ]);

            // Clear cart
            $user->cartItems()->delete();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Order placed successfully! Please complete payment to activate your leases.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
