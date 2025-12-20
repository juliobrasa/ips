<x-app-layout>
    <x-slot name="header">{{ __('IP in Subnet') }}</x-slot>
    <x-slot name="title">{{ __('Check IP in Subnet') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('IP in Subnet') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Check IP Membership') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('IP Address') }}</label>
                        <input type="text" name="ip" value="{{ request('ip') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 192.168.1.50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Subnet CIDR') }}</label>
                        <input type="text" name="cidr" value="{{ request('cidr') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 192.168.1.0/24">
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Check') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Result') }}</h3>
                @if($result)
                <div class="text-center py-8">
                    @if($result['in_subnet'])
                    <div class="w-20 h-20 mx-auto bg-success-100 rounded-full flex items-center justify-center">
                        <span class="material-icons-outlined text-4xl text-success-600">check_circle</span>
                    </div>
                    <p class="mt-4 text-lg font-semibold text-success-700">{{ __('IP is in subnet') }}</p>
                    @else
                    <div class="w-20 h-20 mx-auto bg-danger-100 rounded-full flex items-center justify-center">
                        <span class="material-icons-outlined text-4xl text-danger-600">cancel</span>
                    </div>
                    <p class="mt-4 text-lg font-semibold text-danger-700">{{ __('IP is NOT in subnet') }}</p>
                    @endif
                    <p class="mt-2 text-gray-500">
                        <span class="font-mono">{{ $result['ip'] }}</span>
                        {{ $result['in_subnet'] ? '∈' : '∉' }}
                        <span class="font-mono">{{ $result['cidr'] }}</span>
                    </p>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">{{ __('Enter an IP and subnet to check') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
