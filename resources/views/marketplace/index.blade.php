<x-app-layout>
    <x-slot name="header">{{ __('IP Marketplace') }}</x-slot>
    <x-slot name="title">{{ __('Marketplace') }}</x-slot>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Filters Sidebar -->
        <div class="lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-material-1 p-6 sticky top-24">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Filters') }}</h3>

                <form action="{{ route('marketplace.index') }}" method="GET">
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('IP Address') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="e.g., 192.168.1"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <!-- RIR -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('RIR') }}</label>
                        <select name="rir" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('All RIRs') }}</option>
                            @foreach($rirs as $rir)
                            <option value="{{ $rir }}" {{ request('rir') === $rir ? 'selected' : '' }}>{{ $rir }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CIDR -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Subnet Size') }}</label>
                        <select name="cidr" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('All Sizes') }}</option>
                            @foreach($cidrs as $cidr)
                            <option value="{{ $cidr }}" {{ request('cidr') == $cidr ? 'selected' : '' }}>/{{ $cidr }} ({{ pow(2, 32 - $cidr) }} IPs)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Country') }}</label>
                        <select name="country" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('All Countries') }}</option>
                            @foreach($countries as $country)
                            <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Price per IP/month') }}</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="{{ __('Min') }}" step="0.01" min="0"
                                   class="w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="{{ __('Max') }}" step="0.01" min="0"
                                   class="w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sort By') }}</label>
                        <select name="sort" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                            <option value="reputation" {{ request('sort') === 'reputation' ? 'selected' : '' }}>{{ __('Best Reputation') }}</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                            {{ __('Apply Filters') }}
                        </button>
                        <a href="{{ route('marketplace.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="flex-1">
            <!-- Results Header -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    <span class="font-semibold">{{ $subnets->total() }}</span> {{ __('subnets available') }}
                </p>
            </div>

            <!-- Subnets Grid -->
            @if($subnets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($subnets as $subnet)
                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden hover:shadow-material-2 transition-shadow">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-4 text-white">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold">{{ $subnet->cidr_notation }}</span>
                            <span class="bg-white/20 px-2 py-1 rounded text-sm">{{ $subnet->rir }}</span>
                        </div>
                        <div class="flex items-center mt-2 text-primary-100 text-sm">
                            <span class="material-icons-outlined text-sm mr-1">location_on</span>
                            {{ $subnet->geolocation_country ?? __('Unknown') }}
                            @if($subnet->geolocation_city)
                            , {{ $subnet->geolocation_city }}
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('IPs Available') }}</p>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($subnet->ip_count) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Price per IP') }}</p>
                                <p class="text-xl font-bold text-secondary-600">${{ number_format($subnet->price_per_ip_monthly, 2) }}/mo</p>
                            </div>
                        </div>

                        <!-- Reputation Score -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-500">{{ __('Reputation') }}</span>
                                <span class="font-medium {{ $subnet->reputation_score >= 80 ? 'text-success-600' : ($subnet->reputation_score >= 50 ? 'text-warning-600' : 'text-danger-600') }}">
                                    {{ $subnet->reputation_score }}/100
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $subnet->reputation_score >= 80 ? 'bg-success-500' : ($subnet->reputation_score >= 50 ? 'bg-warning-500' : 'bg-danger-500') }}"
                                     style="width: {{ $subnet->reputation_score }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>{{ __('Min') }}. {{ $subnet->min_lease_months }} {{ $subnet->min_lease_months > 1 ? __('months') : __('month') }}</span>
                            <span>{{ __('Total') }}: ${{ number_format($subnet->total_monthly_price, 2) }}/mo</span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('marketplace.show', $subnet) }}"
                               class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                {{ __('Details') }}
                            </a>
                            <form action="{{ route('cart.add', $subnet) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="lease_months" value="{{ $subnet->min_lease_months }}">
                                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                                    {{ __('Add to Cart') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $subnets->links() }}
            </div>
            @else
            <div class="bg-white rounded-xl shadow-material-1 p-12 text-center">
                <span class="material-icons-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No subnets found') }}</h3>
                <p class="text-gray-500 mb-4">{{ __('Try adjusting your filters or check back later.') }}</p>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                    <span class="material-icons-outlined mr-1">refresh</span>
                    {{ __('Reset Filters') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
