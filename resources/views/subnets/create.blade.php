<x-app-layout>
    <x-slot name="header">{{ __('Add Subnet') }}</x-slot>
    <x-slot name="title">{{ __('List New Subnet') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('subnets.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            {{ __('Back to My Subnets') }}
        </a>

        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Subnet Information') }}</h3>
                <p class="text-gray-500 text-sm">{{ __('Provide details about the IP subnet you want to list.') }}</p>
            </div>

            <form action="{{ route('subnets.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Network Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Network Address') }}</label>
                        <input type="text" name="network_address" value="{{ old('network_address') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 font-mono"
                               placeholder="192.168.1.0">
                        <p class="text-xs text-gray-500 mt-1">{{ __('The starting IP address of the subnet') }}</p>
                        @error('network_address')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CIDR -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('CIDR Notation') }}</label>
                        <select name="cidr" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('Select subnet size...') }}</option>
                            <option value="24" {{ old('cidr') == '24' ? 'selected' : '' }}>/24 - 256 IPs</option>
                            <option value="23" {{ old('cidr') == '23' ? 'selected' : '' }}>/23 - 512 IPs</option>
                            <option value="22" {{ old('cidr') == '22' ? 'selected' : '' }}>/22 - 1,024 IPs</option>
                            <option value="21" {{ old('cidr') == '21' ? 'selected' : '' }}>/21 - 2,048 IPs</option>
                            <option value="20" {{ old('cidr') == '20' ? 'selected' : '' }}>/20 - 4,096 IPs</option>
                            <option value="19" {{ old('cidr') == '19' ? 'selected' : '' }}>/19 - 8,192 IPs</option>
                            <option value="18" {{ old('cidr') == '18' ? 'selected' : '' }}>/18 - 16,384 IPs</option>
                            <option value="17" {{ old('cidr') == '17' ? 'selected' : '' }}>/17 - 32,768 IPs</option>
                            <option value="16" {{ old('cidr') == '16' ? 'selected' : '' }}>/16 - 65,536 IPs</option>
                        </select>
                        @error('cidr')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RIR -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Regional Internet Registry (RIR)') }}</label>
                        <select name="rir" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('Select RIR...') }}</option>
                            <option value="ARIN" {{ old('rir') === 'ARIN' ? 'selected' : '' }}>ARIN ({{ __('North America') }})</option>
                            <option value="RIPE" {{ old('rir') === 'RIPE' ? 'selected' : '' }}>RIPE NCC ({{ __('Europe, Middle East') }})</option>
                            <option value="APNIC" {{ old('rir') === 'APNIC' ? 'selected' : '' }}>APNIC ({{ __('Asia Pacific') }})</option>
                            <option value="LACNIC" {{ old('rir') === 'LACNIC' ? 'selected' : '' }}>LACNIC ({{ __('Latin America') }})</option>
                            <option value="AFRINIC" {{ old('rir') === 'AFRINIC' ? 'selected' : '' }}>AFRINIC ({{ __('Africa') }})</option>
                        </select>
                        @error('rir')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RIR Handle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('RIR Handle / inetnum') }}</label>
                        <input type="text" name="rir_handle" value="{{ old('rir_handle') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="{{ __('e.g.') }}, NET-192-168-1-0-1">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Your RIR registration handle (optional)') }}</p>
                        @error('rir_handle')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Geolocation Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Geolocation Country') }}</label>
                        <select name="geolocation_country" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('Select country...') }}</option>
                            <option value="US" {{ old('geolocation_country') === 'US' ? 'selected' : '' }}>{{ __('United States') }}</option>
                            <option value="GB" {{ old('geolocation_country') === 'GB' ? 'selected' : '' }}>{{ __('United Kingdom') }}</option>
                            <option value="DE" {{ old('geolocation_country') === 'DE' ? 'selected' : '' }}>{{ __('Germany') }}</option>
                            <option value="FR" {{ old('geolocation_country') === 'FR' ? 'selected' : '' }}>{{ __('France') }}</option>
                            <option value="NL" {{ old('geolocation_country') === 'NL' ? 'selected' : '' }}>{{ __('Netherlands') }}</option>
                            <option value="ES" {{ old('geolocation_country') === 'ES' ? 'selected' : '' }}>{{ __('Spain') }}</option>
                            <option value="IT" {{ old('geolocation_country') === 'IT' ? 'selected' : '' }}>{{ __('Italy') }}</option>
                            <option value="CA" {{ old('geolocation_country') === 'CA' ? 'selected' : '' }}>{{ __('Canada') }}</option>
                            <option value="AU" {{ old('geolocation_country') === 'AU' ? 'selected' : '' }}>{{ __('Australia') }}</option>
                            <option value="JP" {{ old('geolocation_country') === 'JP' ? 'selected' : '' }}>{{ __('Japan') }}</option>
                            <option value="BR" {{ old('geolocation_country') === 'BR' ? 'selected' : '' }}>{{ __('Brazil') }}</option>
                            <option value="SG" {{ old('geolocation_country') === 'SG' ? 'selected' : '' }}>{{ __('Singapore') }}</option>
                        </select>
                        @error('geolocation_country')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Geolocation City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Geolocation City') }} ({{ __('Optional') }})</label>
                        <input type="text" name="geolocation_city" value="{{ old('geolocation_city') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="{{ __('e.g.') }}, New York">
                        @error('geolocation_city')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Pricing') }}</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price per IP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Price per IP (Monthly)') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                <input type="number" name="price_per_ip_monthly" value="{{ old('price_per_ip_monthly') }}" required
                                       step="0.01" min="0.01"
                                       class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                       placeholder="0.50">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Market average: $0.40 - $0.80 per IP/month') }}</p>
                            @error('price_per_ip_monthly')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minimum Lease Months -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Minimum Lease Period') }}</label>
                            <select name="min_lease_months" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="1" {{ old('min_lease_months') == '1' ? 'selected' : '' }}>1 {{ __('month') }}</option>
                                <option value="3" {{ old('min_lease_months') == '3' ? 'selected' : '' }}>3 {{ __('months') }}</option>
                                <option value="6" {{ old('min_lease_months') == '6' ? 'selected' : '' }}>6 {{ __('months') }}</option>
                                <option value="12" {{ old('min_lease_months') == '12' ? 'selected' : '' }}>12 {{ __('months') }}</option>
                            </select>
                            @error('min_lease_months')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-primary-50 border border-primary-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-primary-600 mr-3">info</span>
                        <div>
                            <h5 class="font-medium text-primary-800">{{ __('Verification Required') }}</h5>
                            <p class="text-sm text-primary-700 mt-1">
                                {{ __('After submission, your subnet will undergo verification to confirm ownership via RIR records. This process typically takes 24-48 hours. You\'ll receive an email once approved.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 pt-6 border-t border-gray-100 flex justify-end space-x-4">
                    <a href="{{ route('subnets.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center">
                        <span class="material-icons-outlined mr-2">add</span>
                        {{ __('Add Subnet') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
