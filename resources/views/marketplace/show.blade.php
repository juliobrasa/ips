<x-app-layout>
    <x-slot name="header">{{ __('Subnet Details') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }}</x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            {{ __('Back to Marketplace') }}
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header Card -->
                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h1 class="text-3xl font-bold">{{ $subnet->cidr_notation }}</h1>
                            <span class="bg-white/20 px-3 py-1 rounded-lg text-sm font-medium">{{ $subnet->rir }}</span>
                        </div>
                        <div class="flex items-center text-primary-100">
                            <span class="material-icons-outlined mr-2">location_on</span>
                            <span>{{ $subnet->geolocation_country ?? __('Unknown') }}@if($subnet->geolocation_city), {{ $subnet->geolocation_city }}@endif</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('IP Count') }}</p>
                                <p class="text-2xl font-bold text-gray-800">{{ number_format($subnet->ip_count) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Subnet Size') }}</p>
                                <p class="text-2xl font-bold text-gray-800">/{{ $subnet->cidr }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Price per IP') }}</p>
                                <p class="text-2xl font-bold text-secondary-600">${{ number_format($subnet->price_per_ip_monthly, 2) }}/{{ __('mo') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Total Monthly') }}</p>
                                <p class="text-2xl font-bold text-secondary-600">${{ number_format($subnet->total_monthly_price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IP Range Details -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('IP Range Information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 text-sm mb-1">{{ __('Start Address') }}</p>
                            <p class="font-mono text-lg text-gray-800">{{ $subnet->start_ip }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 text-sm mb-1">{{ __('End Address') }}</p>
                            <p class="font-mono text-lg text-gray-800">{{ $subnet->end_ip }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reputation Details -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Reputation & Quality') }}</h3>

                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">{{ __('Overall Reputation Score') }}</span>
                            <span class="text-2xl font-bold {{ $subnet->reputation_score >= 80 ? 'text-success-600' : ($subnet->reputation_score >= 50 ? 'text-warning-600' : 'text-danger-600') }}">
                                {{ $subnet->reputation_score }}/100
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $subnet->reputation_score >= 80 ? 'bg-success-500' : ($subnet->reputation_score >= 50 ? 'bg-warning-500' : 'bg-danger-500') }}"
                                 style="width: {{ $subnet->reputation_score }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <span class="material-icons-outlined text-3xl {{ $subnet->reputation_score >= 80 ? 'text-success-500' : 'text-gray-400' }} mb-2">verified</span>
                            <p class="font-medium text-gray-800">{{ __('Clean History') }}</p>
                            <p class="text-sm text-gray-500">{{ __('No major blocklist issues') }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <span class="material-icons-outlined text-3xl {{ $subnet->rir_verified ? 'text-success-500' : 'text-gray-400' }} mb-2">fact_check</span>
                            <p class="font-medium text-gray-800">{{ __('RIR Verified') }}</p>
                            <p class="text-sm text-gray-500">{{ $subnet->rir_verified ? __('Ownership confirmed') : __('Pending verification') }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <span class="material-icons-outlined text-3xl text-primary-500 mb-2">security</span>
                            <p class="font-medium text-gray-800">{{ __('RPKI Ready') }}</p>
                            <p class="text-sm text-gray-500">{{ __('ROA support available') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Holder Information -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Listed By') }}</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                            <span class="material-icons-outlined text-primary-600">business</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $subnet->company->company_name }}</p>
                            <p class="text-sm text-gray-500">{{ $subnet->company->country }}</p>
                        </div>
                        @if($subnet->company->kyc_verified_at)
                        <span class="ml-auto inline-flex items-center px-3 py-1 bg-success-100 text-success-700 rounded-full text-sm">
                            <span class="material-icons-outlined text-sm mr-1">verified</span>
                            {{ __('Verified Company') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar - Order Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-material-1 p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Lease this Subnet') }}</h3>

                    <form action="{{ route('cart.add', $subnet) }}" method="POST">
                        @csrf

                        <!-- Lease Duration -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Lease Duration') }}</label>
                            <select name="lease_months" id="lease_months"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    onchange="updateTotal()">
                                @for($i = $subnet->min_lease_months; $i <= 24; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? __('months') : __('month') }}</option>
                                @endfor
                            </select>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Minimum') }}: {{ $subnet->min_lease_months }} {{ $subnet->min_lease_months > 1 ? __('months') : __('month') }}</p>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="border-t border-gray-100 pt-4 mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500">{{ __('Price per IP') }}</span>
                                <span class="text-gray-800">${{ number_format($subnet->price_per_ip_monthly, 2) }}/{{ __('mo') }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500">{{ __('Total IPs') }}</span>
                                <span class="text-gray-800">{{ number_format($subnet->ip_count) }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500">{{ __('Monthly Total') }}</span>
                                <span class="text-gray-800" id="monthly_total">${{ number_format($subnet->total_monthly_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-medium pt-2 border-t border-gray-100">
                                <span class="text-gray-800">{{ __('Lease Total') }}</span>
                                <span class="text-secondary-600 text-lg" id="lease_total">${{ number_format($subnet->total_monthly_price * $subnet->min_lease_months, 2) }}</span>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button type="submit" class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">add_shopping_cart</span>
                            {{ __('Add to Cart') }}
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 text-center mt-4">
                        {{ __('You will not be charged until checkout') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const monthlyPrice = {{ $subnet->total_monthly_price }};

        function updateTotal() {
            const months = document.getElementById('lease_months').value;
            const total = monthlyPrice * months;
            document.getElementById('lease_total').textContent = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    </script>
    @endpush
</x-app-layout>
