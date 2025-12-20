<x-app-layout>
    <x-slot name="header">{{ __('IP Tools') }}</x-slot>
    <x-slot name="title">{{ __('IP Tools') }}</x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('IP Tools & Utilities') }}</h2>
            <p class="text-gray-500 mt-1">{{ __('Useful tools for managing IP addresses and subnets') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Subnet Calculator -->
            <a href="{{ route('tools.subnet-calculator') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-primary-600">calculate</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Subnet Calculator') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Calculate subnet details from CIDR') }}</p>
                    </div>
                </div>
            </a>

            <!-- Split Subnet -->
            <a href="{{ route('tools.split-subnet') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-secondary-600">call_split</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Split Subnet') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Divide subnet into smaller blocks') }}</p>
                    </div>
                </div>
            </a>

            <!-- Merge Subnets -->
            <a href="{{ route('tools.merge-subnets') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-success-600">call_merge</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Merge Subnets') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Combine contiguous subnets') }}</p>
                    </div>
                </div>
            </a>

            <!-- Range to CIDR -->
            <a href="{{ route('tools.range-to-cidr') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-info-600">swap_horiz</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Range to CIDR') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Convert IP range to CIDR notation') }}</p>
                    </div>
                </div>
            </a>

            <!-- CIDR to Range -->
            <a href="{{ route('tools.cidr-to-range') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-warning-600">straighten</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('CIDR to Range') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Convert CIDR to IP range') }}</p>
                    </div>
                </div>
            </a>

            <!-- IP in Subnet -->
            <a href="{{ route('tools.ip-in-subnet') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-danger-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-danger-600">search</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('IP in Subnet') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Check if IP is within a subnet') }}</p>
                    </div>
                </div>
            </a>

            <!-- IP Info -->
            <a href="{{ route('tools.ip-info') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-purple-600">info</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('IP Info') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Get detailed IP information') }}</p>
                    </div>
                </div>
            </a>

            <!-- Geofeed Generator -->
            <a href="{{ route('tools.geofeed') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-teal-600">public</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Geofeed Generator') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Generate RFC 8805 geofeed') }}</p>
                    </div>
                </div>
            </a>

            <!-- Looking Glass -->
            <a href="{{ route('tools.looking-glass') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-indigo-600">visibility</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Looking Glass') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Query BGP routes & visibility') }}</p>
                    </div>
                </div>
            </a>

            <!-- Subnets Summary -->
            <a href="{{ route('tools.subnets-summary') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-cyan-600">summarize</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">{{ __('Subnets Summary') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Summarize multiple subnets') }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
