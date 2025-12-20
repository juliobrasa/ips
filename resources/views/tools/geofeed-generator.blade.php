<x-app-layout>
    <x-slot name="header">{{ __('Geofeed Generator') }}</x-slot>
    <x-slot name="title">{{ __('RFC 8805 Geofeed Generator') }}</x-slot>

    <div class="space-y-6" x-data="geofeedGenerator()">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('IP Tools') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Geofeed Generator') }}</span>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Geofeed Entries') }}</h3>
                <button @click="addEntry()" class="text-primary-600 hover:text-primary-700 flex items-center text-sm">
                    <span class="material-icons-outlined mr-1">add</span>
                    {{ __('Add Entry') }}
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(entry, index) in entries" :key="index">
                    <div class="grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Prefix') }} *</label>
                            <input type="text" x-model="entry.prefix"
                                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="192.168.0.0/24">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Country') }} *</label>
                            <input type="text" x-model="entry.country" maxlength="2"
                                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="US">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Region') }}</label>
                            <input type="text" x-model="entry.region"
                                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="US-CA">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('City') }}</label>
                            <input type="text" x-model="entry.city"
                                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="San Francisco">
                        </div>
                        <div class="col-span-1 flex items-end">
                            <button @click="removeEntry(index)" class="p-2 text-danger-600 hover:bg-danger-50 rounded-lg">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex items-center gap-4">
                <button @click="generate()" :disabled="entries.length === 0"
                        class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 disabled:opacity-50 flex items-center">
                    <span class="material-icons-outlined mr-2">code</span>
                    {{ __('Generate Geofeed') }}
                </button>
                <button @click="download()" x-show="output" class="border border-primary-600 text-primary-600 px-6 py-2 rounded-lg hover:bg-primary-50 flex items-center">
                    <span class="material-icons-outlined mr-2">download</span>
                    {{ __('Download CSV') }}
                </button>
            </div>
        </div>

        <div x-show="output" class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Generated Geofeed') }}</h3>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm font-mono" x-text="output"></pre>
        </div>

        <div class="bg-info-50 rounded-xl p-6">
            <h3 class="font-semibold text-info-800 mb-3">{{ __('About Geofeed (RFC 8805)') }}</h3>
            <p class="text-info-700 text-sm">
                {{ __('A geofeed is a CSV file that provides geolocation hints for IP prefixes. It follows RFC 8805 format and can be published in your WHOIS remarks or at a publicly accessible URL.') }}
            </p>
            <div class="mt-4 text-sm text-info-600">
                <p class="font-medium">{{ __('Format') }}: prefix,country_code,region,city,postal_code</p>
                <p class="mt-1">{{ __('Example') }}: 192.0.2.0/24,US,US-CA,San Francisco,</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function geofeedGenerator() {
            return {
                entries: [{ prefix: '', country: '', region: '', city: '' }],
                output: '',

                addEntry() {
                    this.entries.push({ prefix: '', country: '', region: '', city: '' });
                },

                removeEntry(index) {
                    this.entries.splice(index, 1);
                },

                generate() {
                    const lines = this.entries
                        .filter(e => e.prefix && e.country)
                        .map(e => `${e.prefix},${e.country.toUpperCase()},${e.region || ''},${e.city || ''},`);
                    this.output = lines.join('\n');
                },

                download() {
                    const blob = new Blob([this.output], { type: 'text/csv' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'geofeed.csv';
                    a.click();
                    URL.revokeObjectURL(url);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
