<x-app-layout>
    <x-slot name="header">{{ __('Range to CIDR') }}</x-slot>
    <x-slot name="title">{{ __('IP Range to CIDR Converter') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Range to CIDR') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('IP Range') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start IP') }}</label>
                        <input type="text" name="start_ip" value="{{ request('start_ip') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 192.168.1.0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End IP') }}</label>
                        <input type="text" name="end_ip" value="{{ request('end_ip') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 192.168.1.255">
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Convert') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('CIDR Blocks') }}</h3>
                @if($result)
                    @if(isset($result['error']))
                    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                        <p class="text-danger-700">{{ $result['error'] }}</p>
                    </div>
                    @else
                    <div class="space-y-2">
                        @foreach($result as $cidr)
                        <div class="p-3 bg-gray-50 rounded-lg font-mono text-sm">{{ $cidr }}</div>
                        @endforeach
                    </div>
                    @endif
                @else
                <p class="text-gray-500 text-center py-8">{{ __('Enter an IP range to convert') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
