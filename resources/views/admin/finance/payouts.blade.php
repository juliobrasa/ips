<x-admin-layout>
    <x-slot name="header">{{ __('Payout Management') }}</x-slot>
    <x-slot name="title">{{ __('Payout Management') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Total Paid Out') }}</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($stats['total_paid'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Pending Payouts') }}</p>
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
                    <p class="text-sm text-gray-500">{{ __('Processing') }}</p>
                    <p class="text-2xl font-bold text-blue-600">${{ number_format($stats['processing'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-blue-600">sync</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-material-1 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                </select>
            </div>

            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="material-icons-outlined align-middle mr-1">filter_list</span>
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Payouts Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('ID') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Company') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Gross Amount') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Fee') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Net Amount') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Created') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payouts as $payout)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-mono font-semibold text-gray-900">#{{ $payout->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $payout->company->company_name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $payout->company->country ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-900">${{ number_format($payout->gross_amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-red-600">-${{ number_format($payout->fee ?? 0, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-green-600">${{ number_format($payout->net_amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$payout->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($payout->status) }}
                        </span>
                        @if($payout->paid_at)
                        <div class="text-xs text-gray-500 mt-1">{{ $payout->paid_at->format('Y-m-d') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $payout->created_at->format('Y-m-d') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($payout->status === 'pending')
                            <form method="POST" action="{{ route('admin.finance.payouts.process', $payout) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="{{ __('Mark as Processing') }}">
                                    <span class="material-icons-outlined text-lg">sync</span>
                                </button>
                            </form>
                            @endif

                            @if($payout->status === 'processing')
                            <button type="button"
                                    onclick="openCompleteModal({{ $payout->id }}, {{ $payout->net_amount }})"
                                    class="p-2 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                    title="{{ __('Mark as Paid') }}">
                                <span class="material-icons-outlined text-lg">check_circle</span>
                            </button>
                            @endif

                            @if($payout->transaction_id)
                            <span class="text-xs text-gray-500 font-mono" title="{{ __('Transaction ID') }}">
                                {{ Str::limit($payout->transaction_id, 10) }}
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-icons-outlined text-4xl mb-2">payments</span>
                        <p>{{ __('No payouts found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payouts->links() }}
        </div>
    </div>

    <!-- Complete Payout Modal -->
    <div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Complete Payout') }}</h3>
            <p class="text-gray-600 mb-4">{{ __('Amount') }}: <span id="completeAmount" class="font-semibold text-green-600"></span></p>
            <form id="completeForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Transaction ID') }}</label>
                    <input type="text" name="transaction_id" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="{{ __('Enter payment transaction ID...') }}">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeCompleteModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        {{ __('Complete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCompleteModal(payoutId, amount) {
            document.getElementById('completeAmount').textContent = '$' + amount.toFixed(2);
            document.getElementById('completeForm').action = '/admin/finance/payouts/' + payoutId + '/complete';
            document.getElementById('completeModal').classList.remove('hidden');
            document.getElementById('completeModal').classList.add('flex');
        }

        function closeCompleteModal() {
            document.getElementById('completeModal').classList.add('hidden');
            document.getElementById('completeModal').classList.remove('flex');
        }
    </script>
</x-admin-layout>
