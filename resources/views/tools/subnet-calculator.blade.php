<x-app-layout>
    <x-slot name="header">{{ __('Subnet Calculator') }}</x-slot>
    <x-slot name="title">{{ __('Subnet Calculator') }}</x-slot>

    <div class="space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Subnet Calculator') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Input Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Calculate Subnet') }}</h3>

                    <form method="GET" class="space-y-4">
                        <div>
                            <label for="cidr" class="block text-sm font-medium text-gray-700 mb-1">{{ __('CIDR Notation') }}</label>
                            <input type="text" name="cidr" id="cidr"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="192.168.1.0/24"
                                   value="{{ request('cidr') }}">
                        </div>

                        <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">calculate</span>
                            {{ __('Calculate') }}
                        </button>
                    </form>
                </div>

                <!-- Quick Reference -->
                <div class="mt-6 bg-info-50 rounded-xl p-6">
                    <h4 class="font-semibold text-info-800 mb-3">{{ __('Common Prefixes') }}</h4>
                    <div class="space-y-2 text-sm text-info-700">
                        <div class="flex justify-between"><span>/32</span><span>1 IP</span></div>
                        <div class="flex justify-between"><span>/30</span><span>4 IPs</span></div>
                        <div class="flex justify-between"><span>/29</span><span>8 IPs</span></div>
                        <div class="flex justify-between"><span>/28</span><span>16 IPs</span></div>
                        <div class="flex justify-between"><span>/27</span><span>32 IPs</span></div>
                        <div class="flex justify-between"><span>/26</span><span>64 IPs</span></div>
                        <div class="flex justify-between"><span>/25</span><span>128 IPs</span></div>
                        <div class="flex justify-between"><span>/24</span><span>256 IPs</span></div>
                        <div class="flex justify-between"><span>/23</span><span>512 IPs</span></div>
                        <div class="flex justify-between"><span>/22</span><span>1,024 IPs</span></div>
                        <div class="flex justify-between"><span>/16</span><span>65,536 IPs</span></div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="lg:col-span-2">
                @if(isset($result['error']))
                <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                    <p class="text-danger-700">{{ $result['error'] }}</p>
                </div>
                @elseif($result)
                <div class="bg-white rounded-xl shadow-material-1">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Subnet Details') }}</h3>
                        <p class="text-2xl font-mono text-primary-600 mt-2">{{ $result['cidr'] }}</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Network Address') }}</p>
                                    <p class="font-mono text-gray-800">{{ $result['network_address'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Broadcast Address') }}</p>
                                    <p class="font-mono text-gray-800">{{ $result['broadcast_address'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('First Usable IP') }}</p>
                                    <p class="font-mono text-gray-800">{{ $result['first_usable'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Last Usable IP') }}</p>
                                    <p class="font-mono text-gray-800">{{ $result['last_usable'] }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Subnet Mask') }}</p>
                                    <p class="font-mono text-gray-800">{{ $result['subnet_mask'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Wildcard Mask') }}</p>
                                    <p class="font-mono text-gray-800">{{ $result['wildcard_mask'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Total Hosts') }}</p>
                                    <p class="font-mono text-gray-800">{{ number_format($result['total_hosts']) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Usable Hosts') }}</p>
                                    <p class="font-mono text-gray-800">{{ number_format($result['usable_hosts']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('IP Class') }}</p>
                                    <p class="font-medium text-gray-800">{{ $result['ip_class'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Private') }}</p>
                                    <p class="font-medium {{ $result['is_private'] ? 'text-warning-600' : 'text-success-600' }}">
                                        {{ $result['is_private'] ? __('Yes') : __('No') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ __('Binary Mask') }}</p>
                                    <p class="font-mono text-xs text-gray-600 break-all">{{ $result['binary_mask'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white rounded-xl shadow-material-1 p-12 text-center">
                    <span class="material-icons-outlined text-6xl text-gray-300">calculate</span>
                    <p class="mt-4 text-gray-500">{{ __('Enter a CIDR to calculate subnet details') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
