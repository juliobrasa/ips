<x-admin-layout>
    <x-slot name="header">{{ __('Lease Management') }}</x-slot>
    <x-slot name="title">{{ __('Lease Management') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Active Leases') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-yellow-600">schedule</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Expiring Soon') }}</p>
                    <p class="text-2xl font-bold text-warning-600">{{ $stats['expiring_soon'] }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">warning</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Monthly Revenue') }}</p>
                    <p class="text-2xl font-bold text-primary-600">${{ number_format($stats['monthly_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-material-1 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="{{ __('IP or company name...') }}">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>{{ __('Pending Payment') }}</option>
                    <option value="pending_assignment" {{ request('status') == 'pending_assignment' ? 'selected' : '' }}>{{ __('Pending Assignment') }}</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>{{ __('Terminated') }}</option>
                </select>
            </div>

            <!-- Expiring Filter -->
            <div class="flex items-center">
                <label class="flex items-center">
                    <input type="checkbox" name="expiring" value="true" {{ request('expiring') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">{{ __('Expiring in 30 days') }}</span>
                </label>
            </div>

            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="material-icons-outlined align-middle mr-1">search</span>
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Leases Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Subnet') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Lessee') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Holder') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Period') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Price') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($leases as $lease)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-mono font-semibold text-gray-900">{{ $lease->subnet->cidr_notation ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ $lease->subnet->total_ips ?? 0 }} IPs</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $lease->lesseeCompany->company_name ?? 'N/A' }}</div>
                        @if($lease->assigned_asn)
                        <div class="text-xs text-primary-600">AS{{ $lease->assigned_asn }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $lease->holderCompany->company_name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $lease->start_date->format('Y-m-d') }}</div>
                        <div class="text-sm text-gray-500">{{ __('to') }} {{ $lease->end_date->format('Y-m-d') }}</div>
                        @if($lease->status === 'active' && $lease->end_date->lte(now()->addDays(30)))
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-800 mt-1">
                            {{ $lease->end_date->diffForHumans() }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                'pending_assignment' => 'bg-blue-100 text-blue-800',
                                'expired' => 'bg-gray-100 text-gray-800',
                                'terminated' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$lease->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-900">${{ number_format($lease->monthly_price, 2) }}</span>
                        <div class="text-xs text-gray-500">{{ __('per month') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.leases.show', $lease) }}"
                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
                               title="{{ __('View Details') }}">
                                <span class="material-icons-outlined text-lg">visibility</span>
                            </a>

                            @if($lease->status === 'active')
                            <button type="button"
                                    onclick="openExtendModal({{ $lease->id }})"
                                    class="p-2 text-primary-500 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-colors"
                                    title="{{ __('Extend Lease') }}">
                                <span class="material-icons-outlined text-lg">update</span>
                            </button>

                            <button type="button"
                                    onclick="openTerminateModal({{ $lease->id }}, '{{ $lease->subnet->cidr_notation ?? 'Unknown' }}')"
                                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                    title="{{ __('Terminate') }}">
                                <span class="material-icons-outlined text-lg">cancel</span>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-icons-outlined text-4xl mb-2">assignment</span>
                        <p>{{ __('No leases found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $leases->links() }}
        </div>
    </div>

    <!-- Extend Modal -->
    <div id="extendModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Extend Lease') }}</h3>
            <form id="extendForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Extension Period (months)') }}</label>
                    <select name="months" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="1">1 {{ __('month') }}</option>
                        <option value="3">3 {{ __('months') }}</option>
                        <option value="6">6 {{ __('months') }}</option>
                        <option value="12">12 {{ __('months') }}</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeExtendModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        {{ __('Extend') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Terminate Modal -->
    <div id="terminateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Terminate Lease') }}</h3>
            <p class="text-gray-600 mb-4">{{ __('Subnet') }}: <span id="terminateSubnetCidr" class="font-mono font-semibold"></span></p>
            <form id="terminateForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Reason for termination') }}</label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="{{ __('Enter the reason for terminating this lease...') }}"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeTerminateModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        {{ __('Terminate') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openExtendModal(leaseId) {
            document.getElementById('extendForm').action = '/admin/leases/' + leaseId + '/extend';
            document.getElementById('extendModal').classList.remove('hidden');
            document.getElementById('extendModal').classList.add('flex');
        }

        function closeExtendModal() {
            document.getElementById('extendModal').classList.add('hidden');
            document.getElementById('extendModal').classList.remove('flex');
        }

        function openTerminateModal(leaseId, cidr) {
            document.getElementById('terminateSubnetCidr').textContent = cidr;
            document.getElementById('terminateForm').action = '/admin/leases/' + leaseId + '/terminate';
            document.getElementById('terminateModal').classList.remove('hidden');
            document.getElementById('terminateModal').classList.add('flex');
        }

        function closeTerminateModal() {
            document.getElementById('terminateModal').classList.add('hidden');
            document.getElementById('terminateModal').classList.remove('flex');
        }
    </script>
</x-admin-layout>
