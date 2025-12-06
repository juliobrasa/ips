<x-app-layout>
    <x-slot name="header">{{ __('Edit Subnet') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('subnets.show', $subnet) }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            {{ __('Back to Subnet') }}
        </a>

        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Edit') }} {{ $subnet->cidr_notation }}</h3>
                <p class="text-gray-500 text-sm">{{ __('Update your subnet listing details') }}</p>
            </div>

            <form action="{{ route('subnets.update', $subnet) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Fixed Information -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Subnet Information (Read-only)') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('Network') }}</span>
                            <p class="font-mono font-medium text-gray-800">{{ $subnet->network_address }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('CIDR') }}</span>
                            <p class="font-medium text-gray-800">/{{ $subnet->cidr }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('IP Count') }}</span>
                            <p class="font-medium text-gray-800">{{ number_format($subnet->ip_count) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('RIR') }}</span>
                            <p class="font-medium text-gray-800">{{ $subnet->rir }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- RIR Handle -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('RIR Handle / inetnum') }}</label>
                        <input type="text" name="rir_handle" value="{{ old('rir_handle', $subnet->rir_handle) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="{{ __('e.g.') }}, NET-192-168-1-0-1">
                        @error('rir_handle')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Geolocation Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Geolocation Country') }}</label>
                        <select name="geolocation_country" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('Select country...') }}</option>
                            <option value="US" {{ $subnet->geolocation_country === 'US' ? 'selected' : '' }}>{{ __('United States') }}</option>
                            <option value="GB" {{ $subnet->geolocation_country === 'GB' ? 'selected' : '' }}>{{ __('United Kingdom') }}</option>
                            <option value="DE" {{ $subnet->geolocation_country === 'DE' ? 'selected' : '' }}>{{ __('Germany') }}</option>
                            <option value="FR" {{ $subnet->geolocation_country === 'FR' ? 'selected' : '' }}>{{ __('France') }}</option>
                            <option value="NL" {{ $subnet->geolocation_country === 'NL' ? 'selected' : '' }}>{{ __('Netherlands') }}</option>
                            <option value="ES" {{ $subnet->geolocation_country === 'ES' ? 'selected' : '' }}>{{ __('Spain') }}</option>
                            <option value="IT" {{ $subnet->geolocation_country === 'IT' ? 'selected' : '' }}>{{ __('Italy') }}</option>
                            <option value="CA" {{ $subnet->geolocation_country === 'CA' ? 'selected' : '' }}>{{ __('Canada') }}</option>
                            <option value="AU" {{ $subnet->geolocation_country === 'AU' ? 'selected' : '' }}>{{ __('Australia') }}</option>
                            <option value="JP" {{ $subnet->geolocation_country === 'JP' ? 'selected' : '' }}>{{ __('Japan') }}</option>
                            <option value="BR" {{ $subnet->geolocation_country === 'BR' ? 'selected' : '' }}>{{ __('Brazil') }}</option>
                            <option value="SG" {{ $subnet->geolocation_country === 'SG' ? 'selected' : '' }}>{{ __('Singapore') }}</option>
                        </select>
                        @error('geolocation_country')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Geolocation City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Geolocation City') }}</label>
                        <input type="text" name="geolocation_city" value="{{ old('geolocation_city', $subnet->geolocation_city) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="{{ __('e.g.') }}, New York">
                        @error('geolocation_city')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price per IP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Price per IP (Monthly)') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="price_per_ip_monthly" value="{{ old('price_per_ip_monthly', $subnet->price_per_ip_monthly) }}" required
                                   step="0.01" min="0.01"
                                   class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Total monthly') }}: ${{ number_format($subnet->ip_count * $subnet->price_per_ip_monthly, 2) }}</p>
                        @error('price_per_ip_monthly')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Minimum Lease Months -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Minimum Lease Period') }}</label>
                        <select name="min_lease_months" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="1" {{ $subnet->min_lease_months == 1 ? 'selected' : '' }}>1 {{ __('month') }}</option>
                            <option value="3" {{ $subnet->min_lease_months == 3 ? 'selected' : '' }}>3 {{ __('months') }}</option>
                            <option value="6" {{ $subnet->min_lease_months == 6 ? 'selected' : '' }}>6 {{ __('months') }}</option>
                            <option value="12" {{ $subnet->min_lease_months == 12 ? 'selected' : '' }}>12 {{ __('months') }}</option>
                        </select>
                        @error('min_lease_months')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Listing Status') }}</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="available" {{ $subnet->status === 'available' ? 'checked' : '' }}
                                       class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-gray-700">{{ __('Available for lease') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="unlisted" {{ $subnet->status === 'unlisted' ? 'checked' : '' }}
                                       class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-gray-700">{{ __('Unlisted (hidden)') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 pt-6 border-t border-gray-100 flex justify-end space-x-4">
                    <a href="{{ route('subnets.show', $subnet) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
