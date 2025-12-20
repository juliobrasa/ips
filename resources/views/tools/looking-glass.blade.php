<x-app-layout>
    <x-slot name="header">{{ __('Looking Glass') }}</x-slot>
    <x-slot name="title">{{ __('BGP Looking Glass') }}</x-slot>

    <div class="space-y-6" x-data="lookingGlass()">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Looking Glass') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Query Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('BGP Query') }}</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prefix or IP') }}</label>
                            <input type="text" x-model="resource"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g., 8.8.8.0/24 or AS15169">
                        </div>

                        <button @click="query()" :disabled="loading"
                                class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 disabled:opacity-50 flex items-center justify-center">
                            <span x-show="!loading" class="material-icons-outlined mr-2">search</span>
                            <span x-show="loading" class="animate-spin material-icons-outlined mr-2">refresh</span>
                            {{ __('Query') }}
                        </button>
                    </div>

                    <!-- Quick Queries -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-medium text-gray-700 mb-3">{{ __('Quick Queries') }}</h4>
                        <div class="space-y-2">
                            <button @click="resource = '8.8.8.0/24'; query()" class="text-left w-full text-sm text-primary-600 hover:underline">
                                Google DNS (8.8.8.0/24)
                            </button>
                            <button @click="resource = '1.1.1.0/24'; query()" class="text-left w-full text-sm text-primary-600 hover:underline">
                                Cloudflare (1.1.1.0/24)
                            </button>
                            <button @click="resource = 'AS15169'; query()" class="text-left w-full text-sm text-primary-600 hover:underline">
                                Google AS (AS15169)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sources -->
                <div class="mt-6 bg-white rounded-xl shadow-material-1 p-6">
                    <h4 class="font-medium text-gray-700 mb-3">{{ __('Data Sources') }}</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach($servers as $key => $server)
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-success-500 rounded-full mr-2"></span>
                            {{ $server['name'] }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Results -->
            <div class="lg:col-span-2">
                <template x-if="error">
                    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg">
                        <p class="text-danger-700" x-text="error"></p>
                    </div>
                </template>

                <template x-if="result && !error">
                    <div class="space-y-6">
                        <!-- Visibility -->
                        <div class="bg-white rounded-xl shadow-material-1">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800">{{ __('Route Visibility') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500 text-sm">{{ __('Visibility') }}</p>
                                        <p class="text-3xl font-bold text-primary-600" x-text="result.visibility?.visibility || 0"></p>
                                        <p class="text-gray-500 text-xs">{{ __('RIS Peers') }}</p>
                                    </div>
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500 text-sm">{{ __('Announced') }}</p>
                                        <p class="text-2xl font-bold" :class="result.visibility?.announced ? 'text-success-600' : 'text-danger-600'" x-text="result.visibility?.announced ? '{{ __('Yes') }}' : '{{ __('No') }}'"></p>
                                    </div>
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500 text-sm">{{ __('First Seen') }}</p>
                                        <p class="text-sm font-medium text-gray-800" x-text="result.visibility?.first_seen || '-'"></p>
                                    </div>
                                </div>

                                <template x-if="result.visibility?.origins?.length > 0">
                                    <div class="mt-6">
                                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Origin ASNs') }}</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="origin in result.visibility.origins" :key="origin.asn">
                                                <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-sm" x-text="'AS' + origin.asn"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- RPKI Status -->
                        <template x-if="result.consistency">
                            <div class="bg-white rounded-xl shadow-material-1">
                                <div class="p-6 border-b border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Routing Consistency') }}</h3>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-gray-500 text-sm">{{ __('In BGP') }}</p>
                                            <p class="font-medium" :class="result.consistency.in_bgp ? 'text-success-600' : 'text-gray-500'" x-text="result.consistency.in_bgp ? '{{ __('Yes') }}' : '{{ __('No') }}'"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-sm">{{ __('In WHOIS') }}</p>
                                            <p class="font-medium" :class="result.consistency.in_whois ? 'text-success-600' : 'text-gray-500'" x-text="result.consistency.in_whois ? '{{ __('Yes') }}' : '{{ __('No') }}'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- AS Path -->
                        <template x-if="result.as_path">
                            <div class="bg-white rounded-xl shadow-material-1">
                                <div class="p-6 border-b border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ __('AS Path Analysis') }}</h3>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-3 gap-4 text-center">
                                        <div>
                                            <p class="text-gray-500 text-sm">{{ __('Min Length') }}</p>
                                            <p class="text-2xl font-bold text-gray-800" x-text="result.as_path.min_path_length || 0"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-sm">{{ __('Avg Length') }}</p>
                                            <p class="text-2xl font-bold text-primary-600" x-text="(result.as_path.avg_path_length || 0).toFixed(1)"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-sm">{{ __('Max Length') }}</p>
                                            <p class="text-2xl font-bold text-gray-800" x-text="result.as_path.max_path_length || 0"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Looking Glass Data -->
                        <template x-if="result.looking_glass?.rrcs?.length > 0">
                            <div class="bg-white rounded-xl shadow-material-1">
                                <div class="p-6 border-b border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Route Collectors') }}</h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-4 max-h-96 overflow-y-auto">
                                        <template x-for="rrc in result.looking_glass.rrcs.slice(0, 10)" :key="rrc.rrc">
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="font-medium text-gray-800" x-text="rrc.rrc"></p>
                                                        <p class="text-gray-500 text-sm" x-text="rrc.location"></p>
                                                    </div>
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm" x-text="rrc.peer_count + ' peers'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!result && !loading">
                    <div class="bg-white rounded-xl shadow-material-1 p-12 text-center">
                        <span class="material-icons-outlined text-6xl text-gray-300">visibility</span>
                        <p class="mt-4 text-gray-500">{{ __('Enter a prefix or ASN to query BGP information') }}</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function lookingGlass() {
            return {
                resource: '',
                result: null,
                error: null,
                loading: false,

                async query() {
                    if (!this.resource) return;

                    this.loading = true;
                    this.error = null;
                    this.result = null;

                    try {
                        const response = await fetch('{{ route('tools.looking-glass.query') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ resource: this.resource })
                        });

                        const data = await response.json();

                        if (data.error) {
                            this.error = data.error;
                        } else {
                            this.result = data;
                        }
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
