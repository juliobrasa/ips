<x-app-layout>
    <x-slot name="header">{{ __('Invoices') }}</x-slot>
    <x-slot name="title">{{ __('Billing') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Pending Amount') }}</p>
                    <p class="text-3xl font-bold text-warning-600">${{ number_format($stats['pending'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">pending</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Paid This Month') }}</p>
                    <p class="text-3xl font-bold text-success-600">${{ number_format($stats['paidThisMonth'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Paid') }}</p>
                    <p class="text-3xl font-bold text-gray-800">${{ number_format($stats['totalPaid'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-gray-600">receipt_long</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        @if($invoices->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Invoice') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Lease') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Due Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons-outlined text-gray-600 text-sm">receipt</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $invoice->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($invoice->lease)
                            <p class="text-gray-800">{{ $invoice->lease->subnet->cidr_notation }}</p>
                            <p class="text-sm text-gray-500">{{ $invoice->lease->holderCompany->company_name }}</p>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-gray-800">{{ $invoice->due_date->format('M d, Y') }}</p>
                            @if($invoice->status === 'pending' && $invoice->due_date->isPast())
                            <p class="text-sm text-danger-600">{{ __('Overdue by') }} {{ $invoice->due_date->diffForHumans() }}</p>
                            @elseif($invoice->status === 'pending')
                            <p class="text-sm text-gray-500">{{ $invoice->due_date->diffForHumans() }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-800">${{ number_format($invoice->total, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($invoice->status === 'paid')
                            <span class="inline-flex items-center px-2 py-1 bg-success-100 text-success-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-success-500 rounded-full mr-1"></span>
                                {{ __('Paid') }}
                            </span>
                            @elseif($invoice->status === 'pending')
                            @if($invoice->due_date->isPast())
                            <span class="inline-flex items-center px-2 py-1 bg-danger-100 text-danger-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-danger-500 rounded-full mr-1"></span>
                                {{ __('Overdue') }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 bg-warning-100 text-warning-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-warning-500 rounded-full mr-1"></span>
                                {{ __('Pending') }}
                            </span>
                            @endif
                            @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                {{ ucfirst($invoice->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('invoices.show', $invoice) }}" class="p-2 text-gray-400 hover:text-primary-600 transition-colors" title="{{ __('View') }}">
                                    <span class="material-icons-outlined">visibility</span>
                                </a>
                                @if($invoice->status === 'pending')
                                <a href="{{ route('invoices.pay', $invoice) }}" class="bg-primary-600 text-white px-3 py-1 rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium">
                                    {{ __('Pay Now') }}
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $invoices->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-icons-outlined text-6xl text-gray-300 mb-4">receipt_long</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No invoices yet') }}</h3>
            <p class="text-gray-500">{{ __('Your invoices will appear here once you start leasing IP addresses.') }}</p>
        </div>
        @endif
    </div>
</x-app-layout>
