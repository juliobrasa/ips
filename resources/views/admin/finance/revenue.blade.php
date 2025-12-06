<x-admin-layout>
    <x-slot name="header">{{ __('Revenue Report') }}</x-slot>
    <x-slot name="title">{{ __('Revenue Report') }}</x-slot>

    <!-- Period Selector -->
    <div class="bg-white rounded-xl shadow-material-1 p-4 mb-6">
        <form method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">{{ __('Group by') }}:</label>
            <select name="period" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="day" {{ $period == 'day' ? 'selected' : '' }}>{{ __('Day') }}</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>{{ __('Week') }}</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>{{ __('Month') }}</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>{{ __('Year') }}</option>
            </select>
        </form>
    </div>

    <!-- Summary Cards -->
    @php
        $totalRevenue = $data->sum('revenue');
        $totalPayouts = $data->sum('payouts');
        $totalEarnings = $data->sum('platform_earnings');
        $totalInvoices = $data->sum('invoice_count');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Total Revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">trending_up</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Holder Payouts') }}</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($totalPayouts, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-red-600">payments</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Platform Earnings') }}</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($totalEarnings, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">account_balance</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Total Invoices') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalInvoices) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-gray-600">receipt_long</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Revenue Chart') }}</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
            <div class="text-center text-gray-500">
                <span class="material-icons-outlined text-4xl mb-2">show_chart</span>
                <p>{{ __('Chart visualization can be added with Chart.js') }}</p>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Period') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Revenue') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Tax') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Holder Payouts') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Platform Earnings') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Invoices') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($data as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-medium text-gray-900">{{ $row['period'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-gray-900">${{ number_format($row['revenue'], 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-gray-600">${{ number_format($row['tax'], 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-red-600">-${{ number_format($row['payouts'], 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @php
                            $earningsColor = $row['platform_earnings'] >= 0 ? 'text-green-600' : 'text-red-600';
                        @endphp
                        <span class="font-semibold {{ $earningsColor }}">${{ number_format($row['platform_earnings'], 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-gray-600">{{ $row['invoice_count'] }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-icons-outlined text-4xl mb-2">analytics</span>
                        <p>{{ __('No revenue data available') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($data->count() > 0)
            <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-900">{{ __('Total') }}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-600">${{ number_format($data->sum('tax'), 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-red-600">-${{ number_format($totalPayouts, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">${{ number_format($totalEarnings, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-600">{{ number_format($totalInvoices) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-admin-layout>
