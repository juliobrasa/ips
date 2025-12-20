<x-app-layout>
    <x-slot name="header">{{ __('IP Information') }}</x-slot>
    <x-slot name="title">{{ __('IP Address Info') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('IP Info') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('IP Lookup') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('IP Address') }}</label>
                        <input type="text" name="ip" value="{{ request('ip') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 8.8.8.8">
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Lookup') }}
                    </button>
                </form>
            </div>

            @if($result)
            <div class="lg:col-span-2 bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Information for') }} {{ $result['ip'] }}</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('IP Class') }}</p>
                        <p class="font-semibold text-gray-800">{{ $result['class'] }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('RIR') }}</p>
                        <p class="font-semibold text-gray-800">{{ $result['rir'] ?? 'Unknown' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('Private') }}</p>
                        <p class="font-semibold {{ $result['is_private'] ? 'text-warning-600' : 'text-success-600' }}">
                            {{ $result['is_private'] ? __('Yes') : __('No') }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('Reserved') }}</p>
                        <p class="font-semibold {{ $result['is_reserved'] ? 'text-warning-600' : 'text-success-600' }}">
                            {{ $result['is_reserved'] ? __('Yes') : __('No') }}
                        </p>
                    </div>
                    <div class="col-span-2 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('Binary') }}</p>
                        <p class="font-mono text-sm break-all">{{ $result['binary'] }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('Decimal') }}</p>
                        <p class="font-mono">{{ number_format($result['decimal']) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">{{ __('Hexadecimal') }}</p>
                        <p class="font-mono">0x{{ strtoupper($result['hex']) }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="lg:col-span-2 bg-white rounded-xl shadow-material-1 p-12 text-center">
                <span class="material-icons-outlined text-6xl text-gray-300">info</span>
                <p class="mt-4 text-gray-500">{{ __('Enter an IP address to view its information') }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
