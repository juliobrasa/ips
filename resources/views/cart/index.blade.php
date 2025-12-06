<x-app-layout>
    <x-slot name="header">{{ __('Shopping Cart') }}</x-slot>
    <x-slot name="title">{{ __('Cart') }}</x-slot>

    <div class="max-w-5xl mx-auto">
        @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="material-icons-outlined text-primary-600">router</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">{{ $item->subnet->cidr_notation }}</h3>
                                <p class="text-gray-500 text-sm">
                                    {{ $item->subnet->rir }} • {{ $item->subnet->geolocation_country ?? __('Unknown') }}
                                </p>
                                <div class="mt-2 flex items-center space-x-4 text-sm">
                                    <span class="text-gray-600">{{ number_format($item->subnet->ip_count) }} IPs</span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600">${{ number_format($item->subnet->price_per_ip_monthly, 2) }}/IP/mo</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-danger-600 transition-colors" title="{{ __('Remove') }}">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm text-gray-500">{{ __('Lease Duration') }}</label>
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="inline-flex items-center ml-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="lease_months" onchange="this.form.submit()"
                                            class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                                        @for($i = $item->subnet->min_lease_months; $i <= 24; $i++)
                                        <option value="{{ $i }}" {{ $item->lease_months == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i > 1 ? __('months') : __('month') }}
                                        </option>
                                        @endfor
                                    </select>
                                </form>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ __('Subtotal') }}</p>
                                <p class="text-xl font-bold text-secondary-600">
                                    ${{ number_format($item->subnet->total_monthly_price * $item->lease_months, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-material-1 p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Order Summary') }}</h3>

                    <div class="space-y-3 mb-4">
                        @foreach($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item->subnet->cidr_notation }} ({{ $item->lease_months }}mo)</span>
                            <span class="text-gray-800">${{ number_format($item->subnet->total_monthly_price * $item->lease_months, 2) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 mb-6">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-500">{{ __('Subtotal') }}</span>
                            <span class="text-gray-800">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-500">{{ __('Platform Fee') }} (5%)</span>
                            <span class="text-gray-800">${{ number_format($total * 0.05, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg pt-2 border-t border-gray-100">
                            <span class="text-gray-800">{{ __('Total') }}</span>
                            <span class="text-secondary-600">${{ number_format($total * 1.05, 2) }}</span>
                        </div>
                    </div>

                    @if(auth()->user()->company)
                    <form action="{{ route('cart.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">shopping_cart_checkout</span>
                            {{ __('Proceed to Checkout') }}
                        </button>
                    </form>
                    @else
                    <div class="bg-warning-50 border border-warning-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-warning-700">
                            <span class="material-icons-outlined text-sm align-middle mr-1">info</span>
                            {{ __('Complete your company profile before checkout.') }}
                        </p>
                    </div>
                    <a href="{{ route('company.create') }}" class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center justify-center">
                        {{ __('Complete Profile') }}
                    </a>
                    @endif

                    <p class="text-xs text-gray-500 text-center mt-4">
                        {{ __('By proceeding, you agree to our Terms of Service') }}
                    </p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-material-1 p-12 text-center">
            <span class="material-icons-outlined text-6xl text-gray-300 mb-4">shopping_cart</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('Your cart is empty') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('Browse our marketplace to find IP addresses for your needs.') }}</p>
            <a href="{{ route('marketplace.index') }}" class="inline-flex items-center bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <span class="material-icons-outlined mr-2">store</span>
                {{ __('Browse Marketplace') }}
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
