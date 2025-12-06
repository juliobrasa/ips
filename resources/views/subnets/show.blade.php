<x-app-layout>
    <x-slot name="header">{{ __('Subnet Details') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }}</x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('subnets.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            {{ __('Back to My Subnets') }}
        </a>

        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $subnet->cidr_notation }}</h1>
                        <div class="flex items-center text-primary-100">
                            <span class="material-icons-outlined mr-2">location_on</span>
                            <span>{{ $subnet->geolocation_country ?? __('Unknown') }}@if($subnet->geolocation_city), {{ $subnet->geolocation_city }}@endif</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($subnet->status === 'available')
                        <span class="bg-success-500 text-white px-4 py-2 rounded-lg font-medium">{{ __('Available') }}</span>
                        @elseif($subnet->status === 'leased')
                        <span class="bg-secondary-500 text-white px-4 py-2 rounded-lg font-medium">{{ __('Leased') }}</span>
                        @elseif($subnet->status === 'pending_verification')
                        <span class="bg-warning-500 text-white px-4 py-2 rounded-lg font-medium">{{ __('Pending Verification') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('IP Count') }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($subnet->ip_count) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('CIDR') }}</p>
                        <p class="text-xl font-bold text-gray-800">/{{ $subnet->cidr }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('RIR') }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ $subnet->rir }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Price per IP') }}</p>
                        <p class="text-xl font-bold text-secondary-600">${{ number_format($subnet->price_per_ip_monthly, 2) }}/{{ __('mo') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Monthly Revenue') }}</p>
                        <p class="text-xl font-bold text-success-600">${{ number_format($subnet->total_monthly_price, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Technical Details -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Technical Details') }}</h3>

                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">{{ __('Network Address') }}</span>
                        <span class="font-mono text-gray-800">{{ $subnet->network_address }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">{{ __('Start IP') }}</span>
                        <span class="font-mono text-gray-800">{{ $subnet->start_ip }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">{{ __('End IP') }}</span>
                        <span class="font-mono text-gray-800">{{ $subnet->end_ip }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">{{ __('RIR Handle') }}</span>
                        <span class="font-mono text-gray-800">{{ $subnet->rir_handle ?? __('Not set') }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">{{ __('Min. Lease Period') }}</span>
                        <span class="text-gray-800">{{ $subnet->min_lease_months }} {{ $subnet->min_lease_months > 1 ? __('months') : __('month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Reputation & Verification -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Reputation & Verification') }}</h3>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">{{ __('Reputation Score') }}</span>
                        <span class="text-2xl font-bold {{ $subnet->reputation_score >= 80 ? 'text-success-600' : ($subnet->reputation_score >= 50 ? 'text-warning-600' : 'text-danger-600') }}">
                            {{ $subnet->reputation_score }}/100
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full {{ $subnet->reputation_score >= 80 ? 'bg-success-500' : ($subnet->reputation_score >= 50 ? 'bg-warning-500' : 'bg-danger-500') }}"
                             style="width: {{ $subnet->reputation_score }}%"></div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-xl {{ $subnet->rir_verified ? 'text-success-600' : 'text-gray-400' }} mr-2">
                                {{ $subnet->rir_verified ? 'check_circle' : 'pending' }}
                            </span>
                            <span class="text-gray-700">{{ __('RIR Verification') }}</span>
                        </div>
                        <span class="text-sm {{ $subnet->rir_verified ? 'text-success-600' : 'text-warning-600' }}">
                            {{ $subnet->rir_verified ? __('Verified') : __('Pending') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-xl {{ $subnet->blocklist_clean ? 'text-success-600' : 'text-danger-600' }} mr-2">
                                {{ $subnet->blocklist_clean ? 'verified_user' : 'warning' }}
                            </span>
                            <span class="text-gray-700">{{ __('Blocklist Status') }}</span>
                        </div>
                        <span class="text-sm {{ $subnet->blocklist_clean ? 'text-success-600' : 'text-danger-600' }}">
                            {{ $subnet->blocklist_clean ? __('Clean') : __('Issues Found') }}
                        </span>
                    </div>

                    @if($subnet->last_blocklist_check)
                    <p class="text-xs text-gray-500 text-center">{{ __('Last checked') }}: {{ $subnet->last_blocklist_check->diffForHumans() }}</p>
                    @endif
                </div>
            </div>

            <!-- Active Lease (if applicable) -->
            @if($subnet->status === 'leased' && $subnet->activeLeases->count() > 0)
            <div class="bg-white rounded-xl shadow-material-1 p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Current Lease') }}</h3>

                @foreach($subnet->activeLeases as $lease)
                <div class="flex items-center justify-between p-4 bg-secondary-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-secondary-100 rounded-full flex items-center justify-center mr-4">
                            <span class="material-icons-outlined text-secondary-600">business</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $lease->lesseeCompany->company_name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $lease->start_date->format('M d, Y') }} - {{ $lease->end_date->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-secondary-600">${{ number_format($lease->monthly_price, 2) }}/{{ __('mo') }}</p>
                        <a href="{{ route('leases.show', $lease) }}" class="text-sm text-primary-600 hover:text-primary-700">
                            {{ __('View Details') }} →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Actions -->
        @if($subnet->status !== 'leased')
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('subnets.edit', $subnet) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium flex items-center">
                <span class="material-icons-outlined mr-2">edit</span>
                {{ __('Edit Subnet') }}
            </a>
            <form action="{{ route('subnets.destroy', $subnet) }}" method="POST"
                  onsubmit="return confirm('{{ __('Are you sure you want to delete this subnet?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700 transition-colors font-medium flex items-center">
                    <span class="material-icons-outlined mr-2">delete</span>
                    {{ __('Delete Subnet') }}
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
