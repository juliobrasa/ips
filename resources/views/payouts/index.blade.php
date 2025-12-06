<x-app-layout>
    <x-slot name="header">{{ __('Payouts') }}</x-slot>
    <x-slot name="title">{{ __('Earnings') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Pending Payout') }}</p>
                    <p class="text-3xl font-bold text-warning-600">${{ number_format($stats['pending'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">schedule</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('This Month') }}</p>
                    <p class="text-3xl font-bold text-success-600">${{ number_format($stats['thisMonth'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">trending_up</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Earned') }}</p>
                    <p class="text-3xl font-bold text-gray-800">${{ number_format($stats['completed'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-gray-600">account_balance_wallet</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        @if($payouts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payout ID') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Gross') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Fee') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Net') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($payouts as $payout)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons-outlined text-success-600 text-sm">payments</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $payout->payout_number ?? 'PO-' . str_pad($payout->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-sm text-gray-500">{{ $payout->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $payout->period_start?->format('M d') }} - {{ $payout->period_end?->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            ${{ number_format($payout->gross_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-danger-600">
                            -${{ number_format($payout->platform_fee, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-success-600">${{ number_format($payout->net_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payout->status === 'completed')
                            <span class="inline-flex items-center px-2 py-1 bg-success-100 text-success-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-success-500 rounded-full mr-1"></span>
                                {{ __('Completed') }}
                            </span>
                            @elseif($payout->status === 'pending')
                            <span class="inline-flex items-center px-2 py-1 bg-warning-100 text-warning-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-warning-500 rounded-full mr-1"></span>
                                {{ __('Pending') }}
                            </span>
                            @elseif($payout->status === 'processing')
                            <span class="inline-flex items-center px-2 py-1 bg-primary-100 text-primary-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-primary-500 rounded-full mr-1"></span>
                                {{ __('Processing') }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                {{ ucfirst($payout->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('payouts.show', $payout) }}" class="text-primary-600 hover:text-primary-700 font-medium">
                                {{ __('View Details') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $payouts->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-icons-outlined text-6xl text-gray-300 mb-4">payments</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No payouts yet') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('Payouts will appear here once your subnets are leased and payments are processed.') }}</p>
            <a href="{{ route('subnets.index') }}" class="inline-flex items-center bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <span class="material-icons-outlined mr-2">lan</span>
                {{ __('Manage Subnets') }}
            </a>
        </div>
        @endif
    </div>

    <!-- Payout Schedule Info -->
    <div class="mt-6 bg-primary-50 border border-primary-200 rounded-xl p-6">
        <div class="flex items-start">
            <span class="material-icons-outlined text-primary-600 mr-4 text-2xl">info</span>
            <div>
                <h4 class="font-medium text-primary-800">{{ __('Payout Schedule') }}</h4>
                <p class="text-sm text-primary-700 mt-1">
                    {{ __('Payouts are processed on the 1st and 15th of each month. A 10% platform fee is deducted from gross earnings. Minimum payout threshold is $100. Funds are typically transferred within 3-5 business days.') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
