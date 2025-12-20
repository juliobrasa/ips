<x-app-layout>
    <x-slot name="header">{{ __('Routing Info') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }} - {{ __('Routing Info') }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('ripe.index') }}" class="hover:text-primary-600">{{ __('RIPE Management') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('ripe.subnet.info', $subnet) }}" class="hover:text-primary-600">{{ $subnet->cidr_notation }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Routing') }}</span>
        </div>

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Routing Information') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('BGP visibility and routing data for') }} {{ $subnet->cidr_notation }}</p>
            </div>
            <a href="https://stat.ripe.net/{{ $subnet->ip_address }}/{{ $subnet->cidr }}#tabId=routing" target="_blank" class="bg-secondary-600 text-white px-4 py-2 rounded-lg hover:bg-secondary-700 flex items-center">
                <span class="material-icons-outlined mr-2">analytics</span>
                {{ __('View on RIPEstat') }}
            </a>
        </div>

        @if(isset($routingData['error']))
        <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <span class="material-icons-outlined text-danger-500 mr-3">error</span>
                <div>
                    <p class="text-danger-700 font-medium">{{ __('Error fetching routing data') }}</p>
                    <p class="text-danger-600 text-sm">{{ $routingData['error'] }}</p>
                </div>
            </div>
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Routing Status -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Routing Status') }}</h3>
                </div>
                <div class="p-6">
                    @if(isset($routingData['status']['data']))
                    @php $status = $routingData['status']['data']; @endphp
                    <div class="space-y-4">
                        <!-- Visibility -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('RIS Peers Visibility') }}</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $status['visibility']['v4']['total_ris_peers'] ?? 0 }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-{{ ($status['visibility']['v4']['total_ris_peers'] ?? 0) > 0 ? 'success' : 'danger' }}-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-outlined text-{{ ($status['visibility']['v4']['total_ris_peers'] ?? 0) > 0 ? 'success' : 'danger' }}-600">
                                    {{ ($status['visibility']['v4']['total_ris_peers'] ?? 0) > 0 ? 'visibility' : 'visibility_off' }}
                                </span>
                            </div>
                        </div>

                        <!-- First/Last Seen -->
                        @if(isset($status['first_seen']))
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-gray-500 text-xs">{{ __('First Seen') }}</p>
                                <p class="font-medium text-gray-800">{{ $status['first_seen']['time'] ?? '-' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-gray-500 text-xs">{{ __('Last Seen') }}</p>
                                <p class="font-medium text-gray-800">{{ $status['last_seen']['time'] ?? '-' }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Announced -->
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-xs">{{ __('Announced') }}</p>
                            <p class="font-medium text-gray-800">
                                @if(($status['announced'] ?? false))
                                <span class="text-success-600 flex items-center">
                                    <span class="material-icons-outlined text-sm mr-1">check_circle</span>
                                    {{ __('Yes') }}
                                </span>
                                @else
                                <span class="text-danger-600 flex items-center">
                                    <span class="material-icons-outlined text-sm mr-1">cancel</span>
                                    {{ __('No') }}
                                </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <span class="material-icons-outlined text-6xl text-gray-300">router</span>
                        <p class="mt-4 text-gray-500">{{ __('No routing status data available') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- BGP State -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('BGP State') }}</h3>
                </div>
                <div class="p-6">
                    @if(isset($routingData['bgp']['data']['bgp_state']))
                    @php $bgpStates = $routingData['bgp']['data']['bgp_state']; @endphp
                    <div class="space-y-3">
                        @forelse(array_slice($bgpStates, 0, 10) as $state)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <code class="text-sm font-semibold text-gray-800">{{ $state['target_prefix'] ?? '-' }}</code>
                                </div>
                                <span class="text-xs text-gray-500">{{ $state['source_id'] ?? '-' }}</span>
                            </div>
                            @if(isset($state['path']))
                            <div class="mt-2">
                                <span class="text-gray-500 text-xs">{{ __('AS Path:') }}</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($state['path'] as $asn)
                                    <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs rounded">AS{{ $asn }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">{{ __('No BGP states found') }}</p>
                        @endforelse

                        @if(count($bgpStates) > 10)
                        <p class="text-gray-500 text-sm text-center">
                            {{ __('Showing 10 of :total states', ['total' => count($bgpStates)]) }}
                        </p>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <span class="material-icons-outlined text-6xl text-gray-300">hub</span>
                        <p class="mt-4 text-gray-500">{{ __('No BGP state data available') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prefix Overview -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Prefix Overview') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($routingData['overview']['data']))
                @php $overview = $routingData['overview']['data']; @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-sm">{{ __('Resource') }}</p>
                        <p class="text-xl font-bold text-gray-800 font-mono">{{ $overview['resource'] ?? '-' }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-sm">{{ __('Is Less Specific') }}</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ isset($overview['is_less_specific']) ? ($overview['is_less_specific'] ? __('Yes') : __('No')) : '-' }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-sm">{{ __('Announced') }}</p>
                        <p class="text-xl font-bold text-{{ ($overview['announced'] ?? false) ? 'success' : 'danger' }}-600">
                            {{ ($overview['announced'] ?? false) ? __('Yes') : __('No') }}
                        </p>
                    </div>
                </div>

                @if(isset($overview['asns']) && !empty($overview['asns']))
                <div class="mt-6">
                    <h4 class="font-medium text-gray-700 mb-3">{{ __('Origin ASNs') }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($overview['asns'] as $asn)
                        <a href="https://stat.ripe.net/AS{{ $asn['asn'] }}" target="_blank" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 flex items-center">
                            <span>AS{{ $asn['asn'] }}</span>
                            @if(isset($asn['holder']))
                            <span class="ml-2 text-primary-500 text-sm">{{ $asn['holder'] }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                @else
                <div class="text-center py-8">
                    <span class="material-icons-outlined text-6xl text-gray-300">search_off</span>
                    <p class="mt-4 text-gray-500">{{ __('No prefix overview data available') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('ripe.subnet.routes', $subnet) }}" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-info-600">alt_route</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('Route Objects') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('Manage RIPE route objects') }}</p>
                </div>
            </a>

            <a href="https://stat.ripe.net/{{ $subnet->ip_address }}/{{ $subnet->cidr }}#tabId=at-a-glance" target="_blank" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-secondary-600">analytics</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('Full RIPEstat Report') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('Detailed analytics') }}</p>
                </div>
            </a>

            <a href="https://bgp.tools/prefix/{{ $subnet->ip_address }}/{{ $subnet->cidr }}" target="_blank" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">hub</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('BGP.tools') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('External BGP analysis') }}</p>
                </div>
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
