@extends('layouts.admin')

@section('title', __('IP Health Management'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('IP Health Management') }}</h1>
                <p class="text-gray-600">{{ __('Monitor and manage IP reputation across all subnets') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.ip-health.dashboard') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('admin.ip-health.at-risk') }}"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                    {{ __('At Risk') }} ({{ $stats['critical'] + $stats['warning'] }})
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="text-sm text-gray-500">{{ __('Total Subnets') }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
                <div class="text-sm text-green-600">{{ __('Clean') }}</div>
                <div class="text-2xl font-bold text-green-700">{{ $stats['clean'] }}</div>
                <div class="text-xs text-green-500">{{ $stats['clean_percentage'] }}%</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
                <div class="text-sm text-yellow-600">{{ __('Warning') }}</div>
                <div class="text-2xl font-bold text-yellow-700">{{ $stats['warning'] }}</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
                <div class="text-sm text-red-600">{{ __('Critical') }}</div>
                <div class="text-2xl font-bold text-red-700">{{ $stats['critical'] }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm border">
                <div class="text-sm text-gray-500">{{ __('Unchecked') }}</div>
                <div class="text-2xl font-bold text-gray-700">{{ $stats['unchecked'] }}</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg shadow-sm border border-blue-200">
                <div class="text-sm text-blue-600">{{ __('Avg Score') }}</div>
                <div class="text-2xl font-bold text-blue-700">{{ $stats['average_score'] }}</div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white p-4 rounded-lg shadow-sm border mb-6">
            <form action="{{ route('admin.ip-health.schedule-check') }}" method="POST" class="flex items-center space-x-4">
                @csrf
                <span class="text-gray-700 font-medium">{{ __('Schedule Check:') }}</span>
                <select name="hours_threshold" class="rounded-md border-gray-300">
                    <option value="24">{{ __('Older than 24h') }}</option>
                    <option value="48">{{ __('Older than 48h') }}</option>
                    <option value="72">{{ __('Older than 72h') }}</option>
                </select>
                <input type="number" name="batch_size" value="50" min="10" max="200"
                    class="w-24 rounded-md border-gray-300" placeholder="Batch">
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition">
                    {{ __('Start Check') }}
                </button>
            </form>
        </div>

        <!-- Subnets Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <form action="{{ route('admin.ip-health.bulk-check') }}" method="POST" id="bulkForm">
                @csrf
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Subnet') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Company') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Score') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Last Check') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($subnets as $subnet)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="subnet_ids[]" value="{{ $subnet->id }}" class="subnet-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono font-medium">{{ $subnet->cidr_notation }}</div>
                                <div class="text-xs text-gray-500">{{ $subnet->rir }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $subnet->company->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($subnet->reputation_score !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $subnet->reputation_score >= 85 ? 'bg-green-100 text-green-800' :
                                           ($subnet->reputation_score >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $subnet->reputation_score }}/100
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $subnet->status === 'available' ? 'bg-green-100 text-green-800' :
                                       ($subnet->status === 'leased' ? 'bg-blue-100 text-blue-800' :
                                       ($subnet->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($subnet->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                @if($subnet->last_reputation_check)
                                    {{ $subnet->last_reputation_check->diffForHumans() }}
                                @else
                                    <span class="text-yellow-600">{{ __('Never') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.subnets.show', $subnet) }}"
                                        class="text-gray-600 hover:text-gray-900" title="{{ __('View') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.subnets.check-reputation', $subnet) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900" title="{{ __('Check Now') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No subnets found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-4 py-3 bg-gray-50 border-t flex justify-between items-center">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50" id="bulkCheckBtn" disabled>
                        {{ __('Check Selected') }}
                    </button>
                    <div>{{ $subnets->links() }}</div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.subnet-checkbox').forEach(cb => cb.checked = this.checked);
    updateBulkButton();
});

document.querySelectorAll('.subnet-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBulkButton);
});

function updateBulkButton() {
    const checked = document.querySelectorAll('.subnet-checkbox:checked').length;
    document.getElementById('bulkCheckBtn').disabled = checked === 0;
}
</script>
@endsection
