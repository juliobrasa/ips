<x-app-layout>
    <x-slot name="header">{{ __('CIDR to Range') }}</x-slot>
    <x-slot name="title">{{ __('CIDR to IP Range Converter') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('CIDR to Range') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('CIDR Notation') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('CIDR') }}</label>
                        <input type="text" name="cidr" value="{{ request('cidr') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 192.168.1.0/24">
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Convert') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('IP Range') }}</h3>
                @if($result)
                    @if(isset($result['error']))
                    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                        <p class="text-danger-700">{{ $result['error'] }}</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('Start IP') }}</p>
                                <p class="font-mono font-semibold">{{ $result['start'] }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('End IP') }}</p>
                                <p class="font-mono font-semibold">{{ $result['end'] }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-primary-50 rounded-lg">
                            <p class="text-sm text-primary-600">{{ __('Total IPs') }}</p>
                            <p class="font-semibold text-primary-800">{{ number_format($result['total_ips']) }}</p>
                        </div>
                    </div>
                    @endif
                @else
                <p class="text-gray-500 text-center py-8">{{ __('Enter a CIDR to convert') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
