<x-app-layout>
    <x-slot name="header">{{ __('Invoice') }}</x-slot>
    <x-slot name="title">{{ $invoice->invoice_number }}</x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            {{ __('Back to Invoices') }}
        </a>

        <!-- Invoice Document -->
        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">{{ __('INVOICE') }}</h1>
                        <p class="text-primary-100 mt-1">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-right">
                        @if($invoice->status === 'paid')
                        <span class="bg-success-500 text-white px-4 py-2 rounded-lg font-semibold">{{ __('PAID') }}</span>
                        @elseif($invoice->status === 'pending' && $invoice->due_date->isPast())
                        <span class="bg-danger-500 text-white px-4 py-2 rounded-lg font-semibold">{{ __('OVERDUE') }}</span>
                        @else
                        <span class="bg-warning-500 text-white px-4 py-2 rounded-lg font-semibold">{{ __('PENDING') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Billing Info -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">{{ __('From') }}</h3>
                        <p class="font-semibold text-gray-800">Soltia IPS Marketplace</p>
                        <p class="text-gray-600">{{ __('Platform Services') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">{{ __('Bill To') }}</h3>
                        <p class="font-semibold text-gray-800">{{ $invoice->company->company_name }}</p>
                        <p class="text-gray-600">{{ $invoice->company->address }}</p>
                        <p class="text-gray-600">{{ $invoice->company->country }}</p>
                        <p class="text-gray-600">{{ __('Tax ID') }}: {{ $invoice->company->tax_id }}</p>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-3 gap-4 mb-8 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Invoice Date') }}</p>
                        <p class="font-medium text-gray-800">{{ $invoice->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Due Date') }}</p>
                        <p class="font-medium {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-danger-600' : 'text-gray-800' }}">
                            {{ $invoice->due_date->format('M d, Y') }}
                        </p>
                    </div>
                    @if($invoice->paid_at)
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Paid Date') }}</p>
                        <p class="font-medium text-success-600">{{ $invoice->paid_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Line Items -->
                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 text-sm font-medium text-gray-500 uppercase">{{ __('Description') }}</th>
                            <th class="text-center py-3 text-sm font-medium text-gray-500 uppercase">{{ __('Period') }}</th>
                            <th class="text-right py-3 text-sm font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($invoice->lease)
                        <tr class="border-b border-gray-100">
                            <td class="py-4">
                                <p class="font-medium text-gray-800">{{ __('IP Address Lease') }} - {{ $invoice->lease->subnet->cidr_notation }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($invoice->lease->subnet->ip_count) }} {{ __('IP addresses') }} @ ${{ number_format($invoice->lease->subnet->price_per_ip_monthly, 2) }}/IP/{{ __('mo') }}</p>
                            </td>
                            <td class="text-center py-4 text-gray-600">
                                {{ $invoice->period_start?->format('M d') }} - {{ $invoice->period_end?->format('M d, Y') }}
                            </td>
                            <td class="text-right py-4 font-medium text-gray-800">
                                ${{ number_format($invoice->subtotal, 2) }}
                            </td>
                        </tr>
                        @endif

                        @if($invoice->platform_fee > 0)
                        <tr class="border-b border-gray-100">
                            <td class="py-4">
                                <p class="font-medium text-gray-800">{{ __('Platform Fee') }} (5%)</p>
                            </td>
                            <td class="text-center py-4 text-gray-600">-</td>
                            <td class="text-right py-4 font-medium text-gray-800">
                                ${{ number_format($invoice->platform_fee, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">{{ __('Subtotal') }}</span>
                            <span class="text-gray-800">${{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->platform_fee > 0)
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">{{ __('Platform Fee') }}</span>
                            <span class="text-gray-800">${{ number_format($invoice->platform_fee, 2) }}</span>
                        </div>
                        @endif
                        @if($invoice->tax > 0)
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">{{ __('Tax') }}</span>
                            <span class="text-gray-800">${{ number_format($invoice->tax, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-3 border-t-2 border-gray-200 font-bold">
                            <span class="text-gray-800">{{ __('Total') }}</span>
                            <span class="text-secondary-600 text-xl">${{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                @if($invoice->status === 'pending')
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Payment Options') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('invoices.pay', $invoice) }}" class="flex items-center justify-center p-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                            <span class="material-icons-outlined mr-2">credit_card</span>
                            {{ __('Pay with Card') }}
                        </a>
                        <button type="button" class="flex items-center justify-center p-4 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            <span class="material-icons-outlined mr-2">account_balance</span>
                            {{ __('Bank Transfer') }}
                        </button>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-2">{{ __('Bank Transfer Details') }}</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <span class="text-gray-500">{{ __('Bank') }}:</span>
                            <span class="text-gray-800">First National Bank</span>
                            <span class="text-gray-500">{{ __('Account') }}:</span>
                            <span class="text-gray-800">1234567890</span>
                            <span class="text-gray-500">{{ __('Reference') }}:</span>
                            <span class="text-gray-800 font-mono">{{ $invoice->invoice_number }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($invoice->status === 'paid' && $invoice->payment)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Payment Information') }}</h3>
                    <div class="p-4 bg-success-50 rounded-lg">
                        <div class="flex items-center text-success-700">
                            <span class="material-icons-outlined mr-2">check_circle</span>
                            <span>{{ __('Paid on') }} {{ $invoice->paid_at->format('M d, Y') }} {{ __('via') }} {{ ucfirst($invoice->payment->payment_method) }}</span>
                        </div>
                        <p class="text-sm text-success-600 mt-1">{{ __('Transaction ID') }}: {{ $invoice->payment->transaction_id }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    {{ __('Questions? Contact us at') }} billing@soltia.io
                </p>
                <button onclick="window.print()" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                    <span class="material-icons-outlined mr-1">print</span>
                    {{ __('Print Invoice') }}
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
