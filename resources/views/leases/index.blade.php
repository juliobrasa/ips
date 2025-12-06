<x-app-layout>
    <x-slot name="header">{{ __('My Leases') }}</x-slot>
    <x-slot name="title">{{ __('Lease Management') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Active Leases') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Pending') }}</p>
                    <p class="text-3xl font-bold text-warning-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">pending</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total IPs') }}</p>
                    <p class="text-3xl font-bold text-primary-600">{{ number_format($stats['totalIps']) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">dns</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Monthly Cost') }}</p>
                    <p class="text-3xl font-bold text-secondary-600">${{ number_format($stats['monthlyCost'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-secondary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-secondary-600">payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Leases Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        @if($leases->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Subnet') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Holder') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Monthly') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leases as $lease)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons-outlined text-primary-600 text-sm">router</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $lease->subnet->cidr_notation }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($lease->subnet->ip_count) }} IPs</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-gray-800">{{ $lease->holderCompany->company_name }}</p>
                            <p class="text-sm text-gray-500">{{ $lease->holderCompany->country }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-gray-800">{{ $lease->start_date->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-500">{{ __('to') }} {{ $lease->end_date->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-secondary-600">${{ number_format($lease->monthly_price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lease->status === 'active')
                            <span class="inline-flex items-center px-2 py-1 bg-success-100 text-success-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-success-500 rounded-full mr-1"></span>
                                {{ __('Active') }}
                            </span>
                            @elseif($lease->status === 'pending_payment')
                            <span class="inline-flex items-center px-2 py-1 bg-warning-100 text-warning-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-warning-500 rounded-full mr-1"></span>
                                {{ __('Pending Payment') }}
                            </span>
                            @elseif($lease->status === 'pending_assignment')
                            <span class="inline-flex items-center px-2 py-1 bg-primary-100 text-primary-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-primary-500 rounded-full mr-1"></span>
                                {{ __('Pending Assignment') }}
                            </span>
                            @elseif($lease->status === 'expired')
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                {{ __('Expired') }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('leases.show', $lease) }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                                {{ __('View') }}
                                <span class="material-icons-outlined text-sm ml-1">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $leases->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-icons-outlined text-6xl text-gray-300 mb-4">assignment</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No leases yet') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('Start by browsing available IP addresses in our marketplace.') }}</p>
            <a href="{{ route('marketplace.index') }}" class="inline-flex items-center bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <span class="material-icons-outlined mr-2">store</span>
                {{ __('Browse Marketplace') }}
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
