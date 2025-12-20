<x-app-layout>
    <x-slot name="header">{{ __('Subnet RIPE Info') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }} - {{ __('RIPE Info') }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('ripe.index') }}" class="hover:text-primary-600">{{ __('RIPE Management') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ $subnet->cidr_notation }}</span>
        </div>

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $subnet->cidr_notation }}</h2>
                <p class="text-gray-500 mt-1">{{ __('RIPE Database and RIPEstat information') }}</p>
            </div>
            <div class="flex space-x-2">
                <form action="{{ route('ripe.subnet.sync', $subnet) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-secondary-600 text-white px-4 py-2 rounded-lg hover:bg-secondary-700 flex items-center">
                        <span class="material-icons-outlined mr-2">sync</span>
                        {{ __('Sync from RIPE') }}
                    </button>
                </form>
                <a href="{{ route('ripe.subnet.edit', $subnet) }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                    <span class="material-icons-outlined mr-2">edit</span>
                    {{ __('Edit WHOIS') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- RIPE Database Info -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('RIPE Database (inetnum)') }}</h3>
                        <a href="https://apps.db.ripe.net/db-web-ui/lookup?source=ripe&type=inetnum&key={{ urlencode($subnet->ripe_inetnum_key ?? $subnet->cidr_notation) }}" target="_blank" class="text-primary-600 hover:text-primary-800 text-sm">
                            {{ __('View in RIPE') }} &rarr;
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($ripeInfo['error']))
                    <div class="bg-danger-50 text-danger-700 p-4 rounded-lg">
                        <p class="font-medium">{{ __('Error fetching RIPE data') }}</p>
                        <p class="text-sm mt-1">{{ $ripeInfo['error'] }}</p>
                    </div>
                    @elseif(!empty($ripeInfo['objects']))
                    @php $attrs = $ripeInfo['objects'][0]['attributes'] ?? []; @endphp
                    <dl class="space-y-3">
                        @foreach(['inetnum', 'netname', 'descr', 'country', 'org', 'admin-c', 'tech-c', 'status', 'mnt-by', 'mnt-lower', 'mnt-routes', 'created', 'last-modified'] as $key)
                        @if(isset($attrs[$key]))
                        <div class="flex">
                            <dt class="w-32 text-gray-500 text-sm">{{ $key }}:</dt>
                            <dd class="flex-1 text-gray-800">
                                @if(is_array($attrs[$key]))
                                    @foreach($attrs[$key] as $val)
                                    <div>{{ $val }}</div>
                                    @endforeach
                                @else
                                    {{ $attrs[$key] }}
                                @endif
                            </dd>
                        </div>
                        @endif
                        @endforeach
                    </dl>
                    @else
                    <div class="text-center py-8">
                        <span class="material-icons-outlined text-4xl text-gray-300">search_off</span>
                        <p class="mt-2 text-gray-500">{{ __('No RIPE data found') }}</p>
                        <a href="{{ route('ripe.subnet.edit', $subnet) }}" class="mt-2 inline-block text-primary-600 hover:underline">
                            {{ __('Link to RIPE') }} &rarr;
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- RIPEstat Info -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('RIPEstat Data') }}</h3>
                        <a href="https://stat.ripe.net/{{ $subnet->ip_address }}" target="_blank" class="text-primary-600 hover:text-primary-800 text-sm">
                            {{ __('View in RIPEstat') }} &rarr;
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($statInfo['error']))
                    <div class="bg-danger-50 text-danger-700 p-4 rounded-lg">
                        <p class="font-medium">{{ __('Error fetching RIPEstat data') }}</p>
                        <p class="text-sm mt-1">{{ $statInfo['error'] }}</p>
                    </div>
                    @else
                    <div class="space-y-6">
                        <!-- Network Info -->
                        @if(isset($statInfo['network']) && !isset($statInfo['network']['error']))
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">{{ __('Network') }}</h4>
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                <p><span class="text-gray-500">{{ __('Prefix:') }}</span> <code class="bg-white px-2 py-0.5 rounded">{{ $statInfo['network']['prefix'] ?? '-' }}</code></p>
                                <p><span class="text-gray-500">{{ __('ASN:') }}</span> <code class="bg-white px-2 py-0.5 rounded">{{ $statInfo['network']['asn'] ?? '-' }}</code></p>
                            </div>
                        </div>
                        @endif

                        <!-- Geolocation -->
                        @if(isset($statInfo['geolocation']) && !isset($statInfo['geolocation']['error']))
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">{{ __('Geolocation') }}</h4>
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                <p><span class="text-gray-500">{{ __('Country:') }}</span> {{ $statInfo['geolocation']['country'] ?? '-' }}</p>
                                <p><span class="text-gray-500">{{ __('City:') }}</span> {{ $statInfo['geolocation']['city'] ?? '-' }}</p>
                                @if(isset($statInfo['geolocation']['latitude']) && isset($statInfo['geolocation']['longitude']))
                                <p><span class="text-gray-500">{{ __('Coordinates:') }}</span> {{ $statInfo['geolocation']['latitude'] }}, {{ $statInfo['geolocation']['longitude'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Abuse Contact -->
                        @if(isset($statInfo['abuse_contact']) && !isset($statInfo['abuse_contact']['error']))
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">{{ __('Abuse Contact') }}</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p><span class="text-gray-500">{{ __('Email:') }}</span>
                                    @if($statInfo['abuse_contact']['email'])
                                    <a href="mailto:{{ $statInfo['abuse_contact']['email'] }}" class="text-primary-600 hover:underline">{{ $statInfo['abuse_contact']['email'] }}</a>
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Routing -->
                        @if(isset($statInfo['routing']) && !isset($statInfo['routing']['error']))
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">{{ __('Routing') }}</h4>
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                <p><span class="text-gray-500">{{ __('Visibility (RIS Peers):') }}</span> {{ $statInfo['routing']['visibility'] ?? 0 }}</p>
                                <p><span class="text-gray-500">{{ __('First Seen:') }}</span> {{ $statInfo['routing']['first_seen'] ?? '-' }}</p>
                                <p><span class="text-gray-500">{{ __('Last Seen:') }}</span> {{ $statInfo['routing']['last_seen'] ?? '-' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('ripe.subnet.routes', $subnet) }}" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-info-600">alt_route</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('Route Objects') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('Manage BGP routes') }}</p>
                </div>
            </a>

            <a href="{{ route('ripe.subnet.geolocation', $subnet) }}" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">location_on</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('Geolocation') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('View location data') }}</p>
                </div>
            </a>

            <a href="{{ route('ripe.subnet.routing', $subnet) }}" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-secondary-600">router</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('Routing Info') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('BGP state & visibility') }}</p>
                </div>
            </a>

            <a href="{{ route('subnets.show', $subnet) }}" class="bg-white rounded-xl shadow-material-1 p-4 hover:shadow-material-2 transition-shadow flex items-center">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">lan</span>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">{{ __('Subnet Details') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('View in marketplace') }}</p>
                </div>
            </a>
        </div>

        <!-- Local Data -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Local RIPE Data') }}</h3>
                <p class="text-gray-500 text-sm">{{ __('Cached RIPE information stored locally') }}</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('RIPE Key') }}</p>
                        <p class="font-medium text-gray-800">{{ $subnet->ripe_inetnum_key ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Netname') }}</p>
                        <p class="font-medium text-gray-800">{{ $subnet->ripe_netname ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Status') }}</p>
                        <p class="font-medium text-gray-800">{{ $subnet->ripe_status ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Maintainer') }}</p>
                        <p class="font-medium text-gray-800">{{ $subnet->ripe_maintainer ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Organisation') }}</p>
                        <p class="font-medium text-gray-800">{{ $subnet->ripe_org ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Last Synced') }}</p>
                        <p class="font-medium text-gray-800">{{ $subnet->ripe_last_synced_at ? $subnet->ripe_last_synced_at->format('Y-m-d H:i') : __('Never') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
