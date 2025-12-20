<x-app-layout>
    <x-slot name="header">{{ __('Edit RIPE WHOIS') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }} - {{ __('Edit WHOIS') }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('ripe.index') }}" class="hover:text-primary-600">{{ __('RIPE Management') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('ripe.subnet.info', $subnet) }}" class="hover:text-primary-600">{{ $subnet->cidr_notation }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Edit') }}</span>
        </div>

        <!-- Page Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Edit RIPE WHOIS Data') }}</h2>
            <p class="text-gray-500 mt-1">{{ __('Update the inetnum object for') }} {{ $subnet->cidr_notation }}</p>
        </div>

        @if($credentials->isEmpty())
        <div class="bg-warning-50 border-l-4 border-warning-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <span class="material-icons-outlined text-warning-500 mr-3">warning</span>
                <div>
                    <p class="text-warning-700 font-medium">{{ __('No API Credentials') }}</p>
                    <p class="text-warning-600 text-sm">{{ __('You need to add a RIPE API credential before you can edit WHOIS data.') }}</p>
                </div>
            </div>
            <a href="{{ route('ripe.credentials') }}" class="mt-3 inline-block bg-warning-600 text-white px-4 py-2 rounded-lg hover:bg-warning-700">
                {{ __('Add Credential') }}
            </a>
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Edit Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('ripe.subnet.update', $subnet) }}" method="POST" class="bg-white rounded-xl shadow-material-1">
                    @csrf
                    @method('PUT')

                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Inetnum Attributes') }}</h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Credential Selection -->
                        <div>
                            <label for="credential_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('API Credential') }} *</label>
                            <select name="credential_id" id="credential_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">{{ __('Select credential...') }}</option>
                                @foreach($credentials as $credential)
                                <option value="{{ $credential->id }}" {{ old('credential_id') == $credential->id ? 'selected' : '' }}>
                                    {{ $credential->name }} ({{ $credential->maintainer }})
                                </option>
                                @endforeach
                            </select>
                            @error('credential_id')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Netname -->
                            <div>
                                <label for="netname" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Netname') }} *</label>
                                <input type="text" name="netname" id="netname" required
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       pattern="[A-Za-z][A-Za-z0-9_-]*"
                                       value="{{ old('netname', $subnet->ripe_netname) }}"
                                       placeholder="EXAMPLE-NET">
                                <p class="text-gray-500 text-xs mt-1">{{ __('Letters, numbers, hyphens, underscores. Must start with a letter.') }}</p>
                                @error('netname')
                                <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Country -->
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Country') }} *</label>
                                <input type="text" name="country" id="country" required
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       maxlength="2"
                                       value="{{ old('country', 'ES') }}"
                                       placeholder="ES">
                                <p class="text-gray-500 text-xs mt-1">{{ __('ISO 3166-1 alpha-2 country code') }}</p>
                                @error('country')
                                <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="descr" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                            <input type="text" name="descr" id="descr"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   value="{{ old('descr') }}"
                                   placeholder="{{ __('Network description') }}">
                            @error('descr')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Admin-c -->
                            <div>
                                <label for="admin_c" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Admin Contact (admin-c)') }} *</label>
                                <input type="text" name="admin_c" id="admin_c" required
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       value="{{ old('admin_c', auth()->user()->company?->ripe_admin_c) }}"
                                       placeholder="ADMIN-RIPE">
                                @error('admin_c')
                                <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tech-c -->
                            <div>
                                <label for="tech_c" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tech Contact (tech-c)') }} *</label>
                                <input type="text" name="tech_c" id="tech_c" required
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       value="{{ old('tech_c', auth()->user()->company?->ripe_tech_c) }}"
                                       placeholder="TECH-RIPE">
                                @error('tech_c')
                                <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }} *</label>
                            <select name="status" id="status" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $subnet->ripe_status) == $value ? 'selected' : '' }}>
                                    {{ $value }} - {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('status')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Remarks') }}</label>
                            <textarea name="remarks" id="remarks" rows="3"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="{{ __('Optional remarks...') }}">{{ old('remarks') }}</textarea>
                            @error('remarks')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Geolocation -->
                        <div>
                            <label for="geoloc" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Geolocation') }}</label>
                            <input type="text" name="geoloc" id="geoloc"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   value="{{ old('geoloc') }}"
                                   placeholder="40.4168 -3.7038">
                            <p class="text-gray-500 text-xs mt-1">{{ __('Latitude and longitude separated by space (e.g., 40.4168 -3.7038 for Madrid)') }}</p>
                            @error('geoloc')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex justify-between">
                        <a href="{{ route('ripe.subnet.info', $subnet) }}" class="text-gray-600 hover:text-gray-800">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                            <span class="material-icons-outlined mr-2">save</span>
                            {{ __('Update in RIPE') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h4 class="font-semibold text-gray-800 mb-3">{{ __('Subnet Info') }}</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('Range:') }}</dt>
                            <dd class="font-mono">{{ $subnet->cidr_notation }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('IPs:') }}</dt>
                            <dd>{{ number_format(pow(2, 32 - $subnet->cidr)) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('RIR:') }}</dt>
                            <dd>{{ $subnet->rir ?? 'RIPE NCC' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-info-50 rounded-xl p-6">
                    <h4 class="font-semibold text-info-800 mb-3">{{ __('RIPE Object Guidelines') }}</h4>
                    <ul class="text-info-700 text-sm space-y-2">
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">check</span>
                            {{ __('Netname must be unique within your allocation') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">check</span>
                            {{ __('Admin-c and tech-c must exist in RIPE database') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">check</span>
                            {{ __('Status must match your allocation type') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">check</span>
                            {{ __('Country code is used for statistics') }}
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h4 class="font-semibold text-gray-800 mb-3">{{ __('Helpful Links') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="https://apps.db.ripe.net/db-web-ui/myresources/overview" target="_blank" class="text-primary-600 hover:underline flex items-center">
                                <span class="material-icons-outlined text-sm mr-1">open_in_new</span>
                                {{ __('My RIPE Resources') }}
                            </a>
                        </li>
                        <li>
                            <a href="https://www.ripe.net/manage-ips-and-asns/db/support/documentation/ripe-database-documentation" target="_blank" class="text-primary-600 hover:underline flex items-center">
                                <span class="material-icons-outlined text-sm mr-1">open_in_new</span>
                                {{ __('RIPE Database Docs') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
