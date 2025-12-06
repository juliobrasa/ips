<x-app-layout>
    <x-slot name="header">Payout Details</x-slot>
    <x-slot name="title">{{ $payout->payout_number ?? 'PO-' . str_pad($payout->id, 6, '0', STR_PAD_LEFT) }}</x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('payouts.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            Back to Payouts
        </a>

        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-success-600 to-success-700 p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">PAYOUT STATEMENT</h1>
                        <p class="text-success-100 mt-1">{{ $payout->payout_number ?? 'PO-' . str_pad($payout->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right">
                        @if($payout->status === 'completed')
                        <span class="bg-white text-success-600 px-4 py-2 rounded-lg font-semibold">COMPLETED</span>
                        @elseif($payout->status === 'processing')
                        <span class="bg-primary-500 text-white px-4 py-2 rounded-lg font-semibold">PROCESSING</span>
                        @else
                        <span class="bg-warning-500 text-white px-4 py-2 rounded-lg font-semibold">PENDING</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Company Info -->
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Payee</h3>
                    <p class="font-semibold text-gray-800">{{ $payout->company->company_name }}</p>
                    <p class="text-gray-600">{{ $payout->company->address }}</p>
                    <p class="text-gray-600">{{ $payout->company->country }}</p>
                </div>

                <!-- Period & Dates -->
                <div class="grid grid-cols-3 gap-4 mb-8 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-500">Period</p>
                        <p class="font-medium text-gray-800">
                            {{ $payout->period_start?->format('M d') }} - {{ $payout->period_end?->format('M d, Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="font-medium text-gray-800">{{ $payout->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($payout->paid_at)
                    <div>
                        <p class="text-sm text-gray-500">Paid</p>
                        <p class="font-medium text-success-600">{{ $payout->paid_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Earnings Breakdown -->
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Earnings Breakdown</h3>
                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 text-sm font-medium text-gray-500 uppercase">Description</th>
                            <th class="text-right py-3 text-sm font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-4">
                                <p class="font-medium text-gray-800">Lease Revenue</p>
                                <p class="text-sm text-gray-500">Income from IP address leases</p>
                            </td>
                            <td class="text-right py-4 font-medium text-gray-800">
                                ${{ number_format($payout->gross_amount, 2) }}
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4">
                                <p class="font-medium text-gray-800">Platform Fee (10%)</p>
                                <p class="text-sm text-gray-500">Marketplace service fee</p>
                            </td>
                            <td class="text-right py-4 font-medium text-danger-600">
                                -${{ number_format($payout->platform_fee, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Gross Amount</span>
                            <span class="text-gray-800">${{ number_format($payout->gross_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Platform Fee</span>
                            <span class="text-danger-600">-${{ number_format($payout->platform_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-t-2 border-gray-200 font-bold">
                            <span class="text-gray-800">Net Payout</span>
                            <span class="text-success-600 text-xl">${{ number_format($payout->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                @if($payout->status === 'completed')
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h3>
                    <div class="p-4 bg-success-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-success-600">Payment Method</span>
                                <p class="font-medium text-success-800">{{ ucfirst($payout->payment_method ?? 'Bank Transfer') }}</p>
                            </div>
                            <div>
                                <span class="text-success-600">Transaction ID</span>
                                <p class="font-mono text-success-800">{{ $payout->transaction_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($payout->status === 'pending')
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="p-4 bg-warning-50 rounded-lg flex items-start">
                        <span class="material-icons-outlined text-warning-600 mr-3">schedule</span>
                        <div>
                            <h4 class="font-medium text-warning-800">Payout Pending</h4>
                            <p class="text-sm text-warning-700">This payout is scheduled for processing. You'll receive an email once it's completed.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Questions about your payout? Contact support@soltia.io
                </p>
                <button onclick="window.print()" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                    <span class="material-icons-outlined mr-1">print</span>
                    Print Statement
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
