<x-admin-layout>
    <x-slot name="header">{{ __('Subnet Management') }}</x-slot>
    <x-slot name="title">{{ __('Subnet Management') }}</x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-material-1 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="{{ __('IP address or company...') }}">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>{{ __('Pending Verification') }}</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                    <option value="leased" {{ request('status') == 'leased' ? 'selected' : '' }}>{{ __('Leased') }}</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                </select>
            </div>

            <!-- RIR Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('RIR') }}</label>
                <select name="rir" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">{{ __('All RIRs') }}</option>
                    <option value="RIPE" {{ request('rir') == 'RIPE' ? 'selected' : '' }}>RIPE NCC</option>
                    <option value="ARIN" {{ request('rir') == 'ARIN' ? 'selected' : '' }}>ARIN</option>
                    <option value="APNIC" {{ request('rir') == 'APNIC' ? 'selected' : '' }}>APNIC</option>
                    <option value="LACNIC" {{ request('rir') == 'LACNIC' ? 'selected' : '' }}>LACNIC</option>
                    <option value="AFRINIC" {{ request('rir') == 'AFRINIC' ? 'selected' : '' }}>AFRINIC</option>
                </select>
            </div>

            <!-- Reputation Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Reputation') }}</label>
                <select name="reputation" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">{{ __('All') }}</option>
                    <option value="clean" {{ request('reputation') == 'clean' ? 'selected' : '' }}>{{ __('Clean (85+)') }}</option>
                    <option value="warning" {{ request('reputation') == 'warning' ? 'selected' : '' }}>{{ __('Warning (50-84)') }}</option>
                    <option value="critical" {{ request('reputation') == 'critical' ? 'selected' : '' }}>{{ __('Critical (<50)') }}</option>
                </select>
            </div>

            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="material-icons-outlined align-middle mr-1">search</span>
                {{ __('Filter') }}
            </button>

            <!-- Bulk Reputation Check -->
            <form method="POST" action="{{ route('admin.subnets.bulk-reputation') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-warning-600 text-white rounded-lg hover:bg-warning-700 transition-colors">
                    <span class="material-icons-outlined align-middle mr-1">update</span>
                    {{ __('Bulk Check') }}
                </button>
            </form>
        </form>
    </div>

    <!-- Subnets Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Subnet') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Holder') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('RIR') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Reputation') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Price/IP') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($subnets as $subnet)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-mono font-semibold text-gray-900">{{ $subnet->cidr_notation }}</div>
                        <div class="text-sm text-gray-500">{{ $subnet->total_ips }} IPs</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $subnet->company->company_name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $subnet->company->country ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $subnet->rir }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending_verification' => 'bg-yellow-100 text-yellow-800',
                                'available' => 'bg-green-100 text-green-800',
                                'leased' => 'bg-blue-100 text-blue-800',
                                'suspended' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$subnet->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $subnet->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($subnet->reputation_score !== null)
                            @php
                                $scoreColor = $subnet->reputation_score >= 85 ? 'text-green-600' : ($subnet->reputation_score >= 50 ? 'text-yellow-600' : 'text-red-600');
                            @endphp
                            <span class="font-semibold {{ $scoreColor }}">{{ $subnet->reputation_score }}/100</span>
                            <div class="text-xs text-gray-500">
                                {{ $subnet->last_reputation_check ? $subnet->last_reputation_check->diffForHumans() : '' }}
                            </div>
                        @else
                            <span class="text-gray-400">{{ __('Not checked') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-900">${{ number_format($subnet->price_per_ip, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.subnets.show', $subnet) }}"
                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
                               title="{{ __('View Details') }}">
                                <span class="material-icons-outlined text-lg">visibility</span>
                            </a>

                            @if(!$subnet->ownership_verified_at)
                            <form method="POST" action="{{ route('admin.subnets.verify', $subnet) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="p-2 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                        title="{{ __('Verify Ownership') }}">
                                    <span class="material-icons-outlined text-lg">verified</span>
                                </button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('admin.subnets.check-reputation', $subnet) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="p-2 text-warning-500 hover:text-warning-700 hover:bg-warning-50 rounded-lg transition-colors"
                                        title="{{ __('Check Reputation') }}">
                                    <span class="material-icons-outlined text-lg">policy</span>
                                </button>
                            </form>

                            @if($subnet->status !== 'suspended')
                            <button type="button"
                                    onclick="openSuspendModal({{ $subnet->id }}, '{{ $subnet->cidr_notation }}')"
                                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                    title="{{ __('Suspend') }}">
                                <span class="material-icons-outlined text-lg">block</span>
                            </button>
                            @else
                            <form method="POST" action="{{ route('admin.subnets.unsuspend', $subnet) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="p-2 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                        title="{{ __('Unsuspend') }}">
                                    <span class="material-icons-outlined text-lg">check_circle</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-icons-outlined text-4xl mb-2">dns</span>
                        <p>{{ __('No subnets found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $subnets->links() }}
        </div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" x-data="{ show: false }">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Suspend Subnet') }}</h3>
            <p class="text-gray-600 mb-4">{{ __('Subnet') }}: <span id="suspendSubnetCidr" class="font-mono font-semibold"></span></p>
            <form id="suspendForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Reason for suspension') }}</label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="{{ __('Enter the reason for suspending this subnet...') }}"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSuspendModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        {{ __('Suspend') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSuspendModal(subnetId, cidr) {
            document.getElementById('suspendSubnetCidr').textContent = cidr;
            document.getElementById('suspendForm').action = '/admin/subnets/' + subnetId + '/suspend';
            document.getElementById('suspendModal').classList.remove('hidden');
            document.getElementById('suspendModal').classList.add('flex');
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').classList.add('hidden');
            document.getElementById('suspendModal').classList.remove('flex');
        }
    </script>
</x-admin-layout>
