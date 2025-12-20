<x-app-layout>
    <x-slot name="header">{{ __('Merge Subnets') }}</x-slot>
    <x-slot name="title">{{ __('Subnet Merger') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Merge Subnets') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Merge Subnets') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('CIDRs (one per line)') }}</label>
                        <textarea name="cidrs" rows="8"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                                  placeholder="192.168.0.0/25&#10;192.168.0.128/25">{{ request('cidrs') }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Merge') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Result') }}</h3>
                @if($result)
                    @if(isset($result['error']))
                    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                        <p class="text-danger-700">{{ $result['error'] }}</p>
                    </div>
                    @elseif(isset($result['merged']))
                    <div class="p-4 bg-success-50 rounded-lg">
                        <p class="text-success-700 font-semibold">{{ __('Merged CIDR') }}:</p>
                        <p class="mt-2 font-mono text-lg">{{ $result['merged'] }}</p>
                    </div>
                    @endif
                @else
                <p class="text-gray-500 text-center py-8">{{ __('Enter CIDRs to merge') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
