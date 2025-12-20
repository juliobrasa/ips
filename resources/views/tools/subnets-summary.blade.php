<x-app-layout>
    <x-slot name="header">{{ __('Subnets Summary') }}</x-slot>
    <x-slot name="title">{{ __('Summarize Subnets') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Subnets Summary') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Input Subnets') }}</h3>
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('CIDRs (one per line)') }}</label>
                        <textarea name="cidrs" rows="10"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                                  placeholder="192.168.0.0/24&#10;192.168.1.0/24&#10;10.0.0.0/16">{{ request('cidrs') }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        {{ __('Summarize') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Summary') }}</h3>
                @if($result)
                    @if(isset($result['error']))
                    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                        <p class="text-danger-700">{{ $result['error'] }}</p>
                    </div>
                    @else
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg text-center">
                                <p class="text-3xl font-bold text-primary-600">{{ $result['count'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('Subnets') }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg text-center">
                                <p class="text-3xl font-bold text-success-600">{{ number_format($result['total_ips']) }}</p>
                                <p class="text-sm text-gray-500">{{ __('Total IPs') }}</p>
                            </div>
                        </div>

                        @if(!empty($result['by_prefix']))
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">{{ __('By Prefix Length') }}</h4>
                            <div class="space-y-2">
                                @foreach($result['by_prefix'] as $prefix => $data)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="font-mono text-sm">/{{ $prefix }}</span>
                                    <span class="text-gray-600">{{ $data['count'] }} ({{ number_format($data['ips']) }} IPs)</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(!empty($result['ranges']))
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">{{ __('Contiguous Ranges') }}</h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($result['ranges'] as $range)
                                <div class="p-2 bg-gray-50 rounded font-mono text-sm">
                                    {{ $range['start'] }} - {{ $range['end'] }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                @else
                <p class="text-gray-500 text-center py-8">{{ __('Enter CIDRs to summarize') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
