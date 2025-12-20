<x-app-layout>
    <x-slot name="header">{{ __('Referral Earnings') }}</x-slot>
    <x-slot name="title">{{ __('Earnings History') }}</x-slot>

    <div class="space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('referrals.index') }}" class="hover:text-primary-600">{{ __('Referrals') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Earnings') }}</span>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <p class="text-gray-500 text-sm">{{ __('Total Earnings') }}</p>
                <p class="text-3xl font-bold text-gray-800">€{{ number_format($stats['total_earnings'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <p class="text-gray-500 text-sm">{{ __('Pending') }}</p>
                <p class="text-3xl font-bold text-warning-600">€{{ number_format($stats['pending_earnings'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <p class="text-gray-500 text-sm">{{ __('Paid Out') }}</p>
                <p class="text-3xl font-bold text-success-600">€{{ number_format($stats['total_earnings'] - $stats['pending_earnings'], 2) }}</p>
            </div>
        </div>

        <!-- Earnings Table -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('All Earnings') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left py-3 px-6 text-gray-500 font-medium">{{ __('Date') }}</th>
                            <th class="text-left py-3 px-6 text-gray-500 font-medium">{{ __('Referred User') }}</th>
                            <th class="text-left py-3 px-6 text-gray-500 font-medium">{{ __('Source') }}</th>
                            <th class="text-left py-3 px-6 text-gray-500 font-medium">{{ __('Status') }}</th>
                            <th class="text-right py-3 px-6 text-gray-500 font-medium">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rewards as $reward)
                        <tr class="border-b border-gray-50">
                            <td class="py-4 px-6 text-gray-600">{{ $reward->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-6">{{ $reward->referral->referred->name ?? 'Unknown' }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ ucfirst($reward->source_type) }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($reward->status === 'pending') bg-warning-100 text-warning-700
                                    @elseif($reward->status === 'paid') bg-success-100 text-success-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($reward->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right font-medium text-success-600">€{{ number_format($reward->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                {{ __('No earnings yet') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rewards instanceof \Illuminate\Pagination\LengthAwarePaginator && $rewards->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $rewards->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
