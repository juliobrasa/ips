<x-app-layout>
    <x-slot name="header">{{ __('Geolocation') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }} - {{ __('Geolocation') }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('ripe.index') }}" class="hover:text-primary-600">{{ __('RIPE Management') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('ripe.subnet.info', $subnet) }}" class="hover:text-primary-600">{{ $subnet->cidr_notation }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Geolocation') }}</span>
        </div>

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Geolocation Data') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Location information for') }} {{ $subnet->cidr_notation }}</p>
            </div>
            <a href="https://ipmap.ripe.net/result/{{ $subnet->ip_address }}" target="_blank" class="bg-secondary-600 text-white px-4 py-2 rounded-lg hover:bg-secondary-700 flex items-center">
                <span class="material-icons-outlined mr-2">map</span>
                {{ __('View on RIPE IPmap') }}
            </a>
        </div>

        @if(isset($geoData['error']))
        <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <span class="material-icons-outlined text-danger-500 mr-3">error</span>
                <div>
                    <p class="text-danger-700 font-medium">{{ __('Error fetching geolocation data') }}</p>
                    <p class="text-danger-600 text-sm">{{ $geoData['error'] }}</p>
                </div>
            </div>
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Geolocation Data -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('MaxMind GeoLite Data') }}</h3>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Geolocation from MaxMind database') }}</p>
                </div>
                <div class="p-6">
                    @if(isset($geoData['data']['located_resources']) && !empty($geoData['data']['located_resources']))
                    @php
                        $resources = $geoData['data']['located_resources'];
                    @endphp
                    <div class="space-y-6">
                        @foreach($resources as $resource)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <span class="material-icons-outlined text-primary-600 mr-2">location_on</span>
                                <span class="font-semibold text-gray-800">{{ $resource['resource'] ?? '-' }}</span>
                            </div>
                            @if(isset($resource['locations']) && !empty($resource['locations']))
                            <div class="space-y-3">
                                @foreach($resource['locations'] as $location)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-500">{{ __('Country:') }}</span>
                                            <span class="ml-1 font-medium">{{ $location['country'] ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">{{ __('City:') }}</span>
                                            <span class="ml-1 font-medium">{{ $location['city'] ?? '-' }}</span>
                                        </div>
                                        @if(isset($location['latitude']) && isset($location['longitude']))
                                        <div class="col-span-2">
                                            <span class="text-gray-500">{{ __('Coordinates:') }}</span>
                                            <span class="ml-1 font-mono">{{ $location['latitude'] }}, {{ $location['longitude'] }}</span>
                                        </div>
                                        @endif
                                        @if(isset($location['covered_percentage']))
                                        <div class="col-span-2">
                                            <span class="text-gray-500">{{ __('Coverage:') }}</span>
                                            <span class="ml-1">{{ $location['covered_percentage'] }}%</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500 text-sm">{{ __('No location data available') }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <span class="material-icons-outlined text-6xl text-gray-300">location_off</span>
                        <p class="mt-4 text-gray-500">{{ __('No geolocation data found') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Map Placeholder & Info -->
            <div class="space-y-6">
                <!-- Map Card -->
                <div class="bg-white rounded-xl shadow-material-1">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Location Map') }}</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $lat = null;
                            $lon = null;
                            if (isset($geoData['data']['located_resources'][0]['locations'][0])) {
                                $loc = $geoData['data']['located_resources'][0]['locations'][0];
                                $lat = $loc['latitude'] ?? null;
                                $lon = $loc['longitude'] ?? null;
                            }
                        @endphp
                        @if($lat && $lon)
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                            <iframe
                                width="100%"
                                height="100%"
                                frameborder="0"
                                scrolling="no"
                                marginheight="0"
                                marginwidth="0"
                                src="https://www.openstreetmap.org/export/embed.html?bbox={{ $lon - 1 }},{{ $lat - 1 }},{{ $lon + 1 }},{{ $lat + 1 }}&layer=mapnik&marker={{ $lat }},{{ $lon }}"
                                style="border: none;">
                            </iframe>
                        </div>
                        <a href="https://www.openstreetmap.org/?mlat={{ $lat }}&mlon={{ $lon }}#map=10/{{ $lat }}/{{ $lon }}" target="_blank" class="mt-2 text-sm text-primary-600 hover:underline flex items-center">
                            <span class="material-icons-outlined text-sm mr-1">open_in_new</span>
                            {{ __('View larger map') }}
                        </a>
                        @else
                        <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-400">
                                <span class="material-icons-outlined text-4xl">map</span>
                                <p class="mt-2">{{ __('No coordinates available') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- RIPE Geolocation Info -->
                <div class="bg-info-50 rounded-xl p-6">
                    <h4 class="font-semibold text-info-800 mb-3">{{ __('About Geolocation') }}</h4>
                    <ul class="text-info-700 text-sm space-y-2">
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">info</span>
                            {{ __('Geolocation data is provided by MaxMind GeoLite2') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">info</span>
                            {{ __('Data is updated weekly on Tuesdays') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">info</span>
                            {{ __('You can set custom geolocation using the geoloc: attribute in your inetnum object') }}
                        </li>
                    </ul>
                    <a href="{{ route('ripe.subnet.edit', $subnet) }}" class="mt-4 inline-flex items-center text-info-800 font-medium hover:underline">
                        <span class="material-icons-outlined text-sm mr-1">edit</span>
                        {{ __('Set custom geolocation') }}
                    </a>
                </div>

                <!-- Query Info -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h4 class="font-semibold text-gray-800 mb-3">{{ __('Query Information') }}</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('Resource:') }}</dt>
                            <dd class="font-mono">{{ $subnet->ip_address }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('Query Time:') }}</dt>
                            <dd>{{ $geoData['data']['query_time'] ?? now()->toIso8601String() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('Data Source:') }}</dt>
                            <dd>RIPEstat</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
