<x-app-layout>
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <!-- Welcome Banner -->
    @if(!$company)
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-xl p-6 mb-6 text-white shadow-material-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ __('Welcome to Soltia IPS Marketplace!') }}</h2>
                <p class="text-primary-100">{{ __('Complete your company profile to start leasing or monetizing IP addresses.') }}</p>
            </div>
            <a href="{{ route('company.create') }}" class="bg-white text-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-primary-50 transition-colors shadow-material-1">
                {{ __('Complete Profile') }}
            </a>
        </div>
    </div>
    @endif

    <!-- Stats Grid -->
    @if($company)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @if($company->isLessee())
        <!-- Active Leases -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Active Leases') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['activeLeases'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">assignment</span>
                </div>
            </div>
        </div>

        <!-- Total IPs Leased -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total IPs Leased') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['totalIpsLeased'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-secondary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-secondary-600">dns</span>
                </div>
            </div>
        </div>

        <!-- Pending Invoices -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Pending Invoices') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['pendingInvoices'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">receipt</span>
                </div>
            </div>
        </div>
        @endif

        @if($company->isHolder())
        <!-- Total Subnets -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Subnets') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['totalSubnets'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">lan</span>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                {{ $stats['leasedSubnets'] ?? 0 }} {{ __('leased') }}, {{ $stats['availableSubnets'] ?? 0 }} {{ __('available') }}
            </div>
        </div>

        <!-- Total Earnings -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Earnings') }}</p>
                    <p class="text-3xl font-bold text-success-600">${{ number_format($stats['totalEarnings'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">payments</span>
                </div>
            </div>
        </div>

        <!-- Pending Payouts -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Pending Payouts') }}</p>
                    <p class="text-3xl font-bold text-warning-600">${{ number_format($stats['pendingPayouts'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">schedule</span>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Leases -->
        @if($company && $recentLeases->count() > 0)
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Recent Leases') }}</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recentLeases as $lease)
                <a href="{{ route('leases.show', $lease) }}" class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="material-icons-outlined text-primary-600 text-sm">lan</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $lease->subnet->cidr_notation }}</p>
                        <p class="text-sm text-gray-500">{{ $lease->holderCompany->company_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-800">${{ number_format($lease->monthly_price, 2) }}/mo</p>
                        <span class="inline-flex px-2 py-1 text-xs rounded-full
                            {{ $lease->status === 'active' ? 'bg-success-100 text-success-700' : '' }}
                            {{ $lease->status === 'pending_payment' ? 'bg-warning-100 text-warning-700' : '' }}
                            {{ $lease->status === 'pending_assignment' ? 'bg-primary-100 text-primary-700' : '' }}
                        ">
                            {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="p-4 border-t border-gray-100">
                <a href="{{ route('leases.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    {{ __('View all leases') }} →
                </a>
            </div>
        </div>
        @endif

        <!-- Available Subnets (Marketplace Preview) -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Featured Subnets') }}</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($availableSubnets as $subnet)
                <a href="{{ route('marketplace.show', $subnet) }}" class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="material-icons-outlined text-secondary-600 text-sm">router</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $subnet->cidr_notation }}</p>
                        <p class="text-sm text-gray-500">{{ $subnet->rir }} • {{ $subnet->geolocation_country ?? __('Unknown') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-secondary-600">${{ number_format($subnet->price_per_ip_monthly, 2) }}/IP/mo</p>
                        <p class="text-sm text-gray-500">{{ $subnet->ip_count }} IPs</p>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <span class="material-icons-outlined text-4xl mb-2">inventory_2</span>
                    <p>{{ __('No subnets available at the moment') }}</p>
                </div>
                @endforelse
            </div>
            <div class="p-4 border-t border-gray-100">
                <a href="{{ route('marketplace.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    {{ __('Browse marketplace') }} →
                </a>
            </div>
        </div>

        <!-- Recent Invoices -->
        @if($company && $recentInvoices->count() > 0)
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Recent Invoices') }}</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recentInvoices as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="material-icons-outlined text-gray-600 text-sm">receipt_long</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $invoice->invoice_number }}</p>
                        <p class="text-sm text-gray-500">{{ __('Due') }} {{ $invoice->due_date->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-800">${{ number_format($invoice->total, 2) }}</p>
                        <span class="inline-flex px-2 py-1 text-xs rounded-full
                            {{ $invoice->status === 'paid' ? 'bg-success-100 text-success-700' : '' }}
                            {{ $invoice->status === 'pending' ? 'bg-warning-100 text-warning-700' : '' }}
                            {{ $invoice->status === 'overdue' ? 'bg-danger-100 text-danger-700' : '' }}
                        ">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="p-4 border-t border-gray-100">
                <a href="{{ route('invoices.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    {{ __('View all invoices') }} →
                </a>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Quick Actions') }}</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-4">
                <a href="{{ route('marketplace.index') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                    <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">store</span>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">{{ __('Browse IPs') }}</span>
                </a>
                @if($company && $company->isHolder())
                <a href="{{ route('subnets.create') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                    <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">add_circle</span>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">{{ __('List Subnet') }}</span>
                </a>
                @endif
                <a href="{{ route('company.edit') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                    <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">business</span>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">{{ __('Company') }}</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                    <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">settings</span>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">{{ __('Settings') }}</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
