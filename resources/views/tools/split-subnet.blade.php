<x-app-layout>
    <x-slot name="header">{{ __('Split Subnet') }}</x-slot>
    <x-slot name="title">{{ __('Subnet Splitter') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Split Subnet') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Input Form -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Split Subnet') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('CIDR') }}</label>
                        <input type="text" name="cidr" value="{{ request('cidr') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 192.168.0.0/24">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('New Prefix Length') }}</label>
                        <input type="number" name="new_prefix" value="{{ request('new_prefix') }}" min="1" max="32"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 26">
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Split') }}
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Results') }}</h3>
                @if($result)
                    @if(isset($result['error']))
                    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                        <p class="text-danger-700">{{ $result['error'] }}</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            {{ __('Split into :count subnets', ['count' => count($result)]) }}
                        </p>
                        <div class="max-h-96 overflow-y-auto space-y-2">
                            @foreach($result as $subnet)
                            <div class="p-3 bg-gray-50 rounded-lg font-mono text-sm">
                                {{ $subnet }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @else
                <p class="text-gray-500 text-center py-8">{{ __('Enter a CIDR and new prefix length to split') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
