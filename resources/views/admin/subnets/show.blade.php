<x-admin-layout>
    <x-slot name="header">{{ __('Subnet Details') }}: {{ $subnet->cidr_notation }}</x-slot>
    <x-slot name="title">{{ __('Subnet Details') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Subnet Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">dns</span>
                    {{ __('Subnet Information') }}
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('IP Range') }}</label>
                        <p class="font-mono font-semibold text-lg">{{ $subnet->cidr_notation }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Total IPs') }}</label>
                        <p class="font-semibold">{{ number_format($subnet->total_ips) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('RIR') }}</label>
                        <p class="font-semibold">{{ $subnet->rir }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Status') }}</label>
                        @php
                            $statusColors = [
                                'pending_verification' => 'bg-yellow-100 text-yellow-800',
                                'available' => 'bg-green-100 text-green-800',
                                'leased' => 'bg-blue-100 text-blue-800',
                                'suspended' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$subnet->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $subnet->status)) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Price per IP') }}</label>
                        <p class="font-semibold text-lg">${{ number_format($subnet->price_per_ip, 2) }}/{{ __('month') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Ownership Verified') }}</label>
                        @if($subnet->ownership_verified_at)
                            <span class="text-green-600 flex items-center">
                                <span class="material-icons-outlined mr-1">verified</span>
                                {{ $subnet->ownership_verified_at->format('Y-m-d H:i') }}
                            </span>
                        @else
                            <span class="text-yellow-600">{{ __('Not verified') }}</span>
                        @endif
                    </div>
                </div>

                @if($subnet->description)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="block text-sm text-gray-500 mb-1">{{ __('Description') }}</label>
                    <p class="text-gray-700 whitespace-pre-line">{{ $subnet->description }}</p>
                </div>
                @endif
            </div>

            <!-- WHOIS Data -->
            @if($whoisData)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">search</span>
                    {{ __('WHOIS Information') }}
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    @if(isset($whoisData['netname']))
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Network Name') }}</label>
                        <p class="font-semibold">{{ $whoisData['netname'] }}</p>
                    </div>
                    @endif
                    @if(isset($whoisData['org']))
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Organization') }}</label>
                        <p class="font-semibold">{{ $whoisData['org'] }}</p>
                    </div>
                    @endif
                    @if(isset($whoisData['country']))
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Country') }}</label>
                        <p class="font-semibold">{{ $whoisData['country'] }}</p>
                    </div>
                    @endif
                    @if(isset($whoisData['abuse_email']))
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Abuse Email') }}</label>
                        <p class="font-semibold">{{ $whoisData['abuse_email'] }}</p>
                    </div>
                    @endif
                </div>

                @if(isset($whoisData['raw']))
                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-primary-600 hover:text-primary-700">{{ __('Show raw WHOIS') }}</summary>
                    <pre class="mt-2 p-4 bg-gray-50 rounded-lg text-xs overflow-x-auto">{{ $whoisData['raw'] }}</pre>
                </details>
                @endif
            </div>
            @endif

            <!-- Reputation Details -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-icons-outlined mr-2 text-primary-600">security</span>
                        {{ __('IP Reputation') }}
                    </h3>
                    <form method="POST" action="{{ route('admin.subnets.check-reputation', $subnet) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                            <span class="material-icons-outlined align-middle mr-1 text-sm">refresh</span>
                            {{ __('Refresh') }}
                        </button>
                    </form>
                </div>

                @if($subnet->reputation_score !== null)
                <div class="flex items-center gap-6 mb-6">
                    <div class="relative w-24 h-24">
                        @php
                            $score = $subnet->reputation_score;
                            $color = $score >= 85 ? '#22c55e' : ($score >= 50 ? '#eab308' : '#ef4444');
                            $circumference = 2 * 3.14159 * 40;
                            $offset = $circumference - ($score / 100) * $circumference;
                        @endphp
                        <svg class="w-24 h-24 transform -rotate-90">
                            <circle cx="48" cy="48" r="40" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                            <circle cx="48" cy="48" r="40" stroke="{{ $color }}" stroke-width="8" fill="none"
                                    stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold" style="color: {{ $color }}">{{ $score }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Last checked') }}</p>
                        <p class="font-medium">{{ $subnet->last_reputation_check ? $subnet->last_reputation_check->format('Y-m-d H:i') : 'N/A' }}</p>
                    </div>
                </div>

                @if($reputationDetails && count($reputationDetails) > 0)
                <div class="space-y-2">
                    <h4 class="text-sm font-medium text-gray-700">{{ __('Blocklist Results') }}</h4>
                    @foreach($reputationDetails as $blocklist => $result)
                    <div class="flex items-center justify-between py-2 px-3 rounded-lg {{ $result['listed'] ? 'bg-red-50' : 'bg-green-50' }}">
                        <span class="text-sm font-medium">{{ $blocklist }}</span>
                        @if($result['listed'])
                            <span class="text-red-600 flex items-center text-sm">
                                <span class="material-icons-outlined text-sm mr-1">warning</span>
                                {{ __('Listed') }}
                            </span>
                        @else
                            <span class="text-green-600 flex items-center text-sm">
                                <span class="material-icons-outlined text-sm mr-1">check</span>
                                {{ __('Clean') }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <p class="text-gray-500">{{ __('Reputation not checked yet. Click Refresh to check.') }}</p>
                @endif
            </div>

            <!-- Leases History -->
            @if($subnet->leases->count() > 0)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">history</span>
                    {{ __('Lease History') }}
                </h3>

                <div class="space-y-3">
                    @foreach($subnet->leases as $lease)
                    <div class="flex items-center justify-between py-3 px-4 rounded-lg bg-gray-50">
                        <div>
                            <p class="font-medium">{{ $lease->lesseeCompany->company_name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $lease->start_date->format('Y-m-d') }} - {{ $lease->end_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $lease->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($lease->status) }}
                            </span>
                            <p class="text-sm text-gray-500">${{ number_format($lease->monthly_price, 2) }}/{{ __('mo') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Holder Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">business</span>
                    {{ __('Holder Information') }}
                </h3>

                @if($subnet->company)
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Company') }}</label>
                        <p class="font-semibold">{{ $subnet->company->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Country') }}</label>
                        <p>{{ $subnet->company->country }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('KYC Status') }}</label>
                        @php
                            $kycColors = [
                                'approved' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_review' => 'bg-blue-100 text-blue-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kycColors[$subnet->company->kyc_status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $subnet->company->kyc_status)) }}
                        </span>
                    </div>
                    @if($subnet->company->user)
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Contact') }}</label>
                        <p>{{ $subnet->company->user->email }}</p>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-gray-500">{{ __('No company information available') }}</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>

                <div class="space-y-3">
                    @if(!$subnet->ownership_verified_at)
                    <form method="POST" action="{{ route('admin.subnets.verify', $subnet) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">verified</span>
                            {{ __('Verify Ownership') }}
                        </button>
                    </form>
                    @endif

                    @if($subnet->status !== 'suspended')
                    <button type="button" onclick="document.getElementById('suspendForm').classList.toggle('hidden')"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">block</span>
                        {{ __('Suspend Subnet') }}
                    </button>

                    <form id="suspendForm" method="POST" action="{{ route('admin.subnets.suspend', $subnet) }}" class="hidden">
                        @csrf
                        <div class="mt-3 p-4 bg-red-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Suspension Reason') }}</label>
                            <textarea name="reason" rows="2" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                            <button type="submit" class="mt-2 w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                {{ __('Confirm Suspension') }}
                            </button>
                        </div>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.subnets.unsuspend', $subnet) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">check_circle</span>
                            {{ __('Unsuspend Subnet') }}
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('admin.subnets.index') }}"
                       class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <span class="material-icons-outlined mr-2 align-middle">arrow_back</span>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
