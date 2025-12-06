<x-admin-layout>
    <x-slot name="header">{{ __('Invoice Management') }}</x-slot>
    <x-slot name="title">{{ __('Invoice Management') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Total Invoiced') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_invoiced'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-gray-600">receipt_long</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Paid') }}</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($stats['paid'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Pending') }}</p>
                    <p class="text-2xl font-bold text-yellow-600">${{ number_format($stats['pending'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-yellow-600">schedule</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Overdue') }}</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($stats['overdue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-red-600">warning</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-material-1 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>

            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="material-icons-outlined align-middle mr-1">search</span>
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Invoice') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Company') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Subnet') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Date') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Due Date') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Amount') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-mono font-semibold text-gray-900">{{ $invoice->invoice_number }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $invoice->company->company_name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm">{{ $invoice->lease->subnet->cidr_notation ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $invoice->created_at->format('Y-m-d') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A' }}</span>
                        @if($invoice->status === 'pending' && $invoice->due_date && $invoice->due_date->isPast())
                            <span class="text-red-600 text-xs block">{{ __('Overdue') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">${{ number_format($invoice->total, 2) }}</div>
                        @if($invoice->tax > 0)
                        <div class="text-xs text-gray-500">{{ __('Tax') }}: ${{ number_format($invoice->tax, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'paid' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'overdue' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
                               title="{{ __('View') }}">
                                <span class="material-icons-outlined text-lg">visibility</span>
                            </a>
                            <a href="{{ route('invoices.download', $invoice) }}"
                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
                               title="{{ __('Download') }}">
                                <span class="material-icons-outlined text-lg">download</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-icons-outlined text-4xl mb-2">receipt_long</span>
                        <p>{{ __('No invoices found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
    </div>
</x-admin-layout>
