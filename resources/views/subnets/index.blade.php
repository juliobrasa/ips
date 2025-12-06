<x-app-layout>
    <x-slot name="header">{{ __('My Subnets') }}</x-slot>
    <x-slot name="title">{{ __('Subnet Management') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Subnets') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">lan</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Available') }}</p>
                    <p class="text-3xl font-bold text-success-600">{{ $stats['available'] }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Leased') }}</p>
                    <p class="text-3xl font-bold text-secondary-600">{{ $stats['leased'] }}</p>
                </div>
                <div class="w-12 h-12 bg-secondary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-secondary-600">handshake</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Monthly Revenue') }}</p>
                    <p class="text-3xl font-bold text-warning-600">${{ number_format($stats['revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <p class="text-gray-600">{{ __('Manage your IP address subnets') }}</p>
        <a href="{{ route('subnets.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center">
            <span class="material-icons-outlined mr-2">add</span>
            {{ __('Add Subnet') }}
        </a>
    </div>

    <!-- Subnets Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        @if($subnets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Subnet') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('RIR') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('IPs') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price/IP') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Reputation') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($subnets as $subnet)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons-outlined text-primary-600 text-sm">router</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $subnet->cidr_notation }}</p>
                                    <p class="text-sm text-gray-500">{{ $subnet->geolocation_country ?? __('Unknown') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">{{ $subnet->rir }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ number_format($subnet->ip_count) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-secondary-600">${{ number_format($subnet->price_per_ip_monthly, 2) }}/mo</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($subnet->status === 'available')
                            <span class="inline-flex items-center px-2 py-1 bg-success-100 text-success-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-success-500 rounded-full mr-1"></span>
                                {{ __('Available') }}
                            </span>
                            @elseif($subnet->status === 'leased')
                            <span class="inline-flex items-center px-2 py-1 bg-secondary-100 text-secondary-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-secondary-500 rounded-full mr-1"></span>
                                {{ __('Leased') }}
                            </span>
                            @elseif($subnet->status === 'pending_verification')
                            <span class="inline-flex items-center px-2 py-1 bg-warning-100 text-warning-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-warning-500 rounded-full mr-1"></span>
                                {{ __('Pending') }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                {{ ucfirst($subnet->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full {{ $subnet->reputation_score >= 80 ? 'bg-success-500' : ($subnet->reputation_score >= 50 ? 'bg-warning-500' : 'bg-danger-500') }}"
                                         style="width: {{ $subnet->reputation_score }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $subnet->reputation_score }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('subnets.show', $subnet) }}" class="p-2 text-gray-400 hover:text-primary-600 transition-colors" title="{{ __('View') }}">
                                    <span class="material-icons-outlined text-xl">visibility</span>
                                </a>
                                @if($subnet->status !== 'leased')
                                <a href="{{ route('subnets.edit', $subnet) }}" class="p-2 text-gray-400 hover:text-primary-600 transition-colors" title="{{ __('Edit') }}">
                                    <span class="material-icons-outlined text-xl">edit</span>
                                </a>
                                <form action="{{ route('subnets.destroy', $subnet) }}" method="POST" class="inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this subnet?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-danger-600 transition-colors" title="{{ __('Delete') }}">
                                        <span class="material-icons-outlined text-xl">delete</span>
                                    </button>
                                </form>
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
            {{ $subnets->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-icons-outlined text-6xl text-gray-300 mb-4">lan</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No subnets yet') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('Start monetizing your IP addresses by adding your first subnet.') }}</p>
            <a href="{{ route('subnets.create') }}" class="inline-flex items-center bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <span class="material-icons-outlined mr-2">add</span>
                {{ __('Add Your First Subnet') }}
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
