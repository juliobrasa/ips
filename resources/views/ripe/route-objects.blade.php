<x-app-layout>
    <x-slot name="header">{{ __('Route Objects') }}</x-slot>
    <x-slot name="title">{{ $subnet->cidr_notation }} - {{ __('Route Objects') }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('ripe.index') }}" class="hover:text-primary-600">{{ __('RIPE Management') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('ripe.subnet.info', $subnet) }}" class="hover:text-primary-600">{{ $subnet->cidr_notation }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Route Objects') }}</span>
        </div>

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Route Objects') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('BGP route object management for') }} {{ $subnet->cidr_notation }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Existing Routes -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-material-1">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Existing Route Objects') }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('Route objects registered in RIPE database for this prefix') }}</p>
                    </div>
                    <div class="p-6">
                        @if(empty($routes))
                        <div class="text-center py-8">
                            <span class="material-icons-outlined text-6xl text-gray-300">alt_route</span>
                            <p class="mt-4 text-gray-500">{{ __('No route objects found') }}</p>
                            <p class="text-gray-400 text-sm">{{ __('Create a route object to authorize BGP announcements') }}</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($routes as $route)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="material-icons-outlined text-gray-400 mr-2">alt_route</span>
                                            <code class="font-semibold text-gray-800">{{ $route['attributes']['route'] ?? $route['attributes']['route6'] ?? '-' }}</code>
                                        </div>
                                        <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">{{ __('Origin:') }}</span>
                                                <code class="ml-1 bg-gray-100 px-2 py-0.5 rounded">{{ $route['attributes']['origin'] ?? '-' }}</code>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">{{ __('Maintainer:') }}</span>
                                                <span class="ml-1">
                                                    @if(is_array($route['attributes']['mnt-by'] ?? null))
                                                        {{ implode(', ', $route['attributes']['mnt-by']) }}
                                                    @else
                                                        {{ $route['attributes']['mnt-by'] ?? '-' }}
                                                    @endif
                                                </span>
                                            </div>
                                            @if(isset($route['attributes']['descr']))
                                            <div class="col-span-2">
                                                <span class="text-gray-500">{{ __('Description:') }}</span>
                                                <span class="ml-1">
                                                    @if(is_array($route['attributes']['descr']))
                                                        {{ implode(', ', $route['attributes']['descr']) }}
                                                    @else
                                                        {{ $route['attributes']['descr'] }}
                                                    @endif
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="https://apps.db.ripe.net/db-web-ui/lookup?source=ripe&type=route&key={{ urlencode(($route['attributes']['route'] ?? '') . ($route['attributes']['origin'] ?? '')) }}" target="_blank" class="text-primary-600 hover:text-primary-800">
                                        <span class="material-icons-outlined">open_in_new</span>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Create Route Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-material-1">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Create Route Object') }}</h3>
                    </div>

                    @if($credentials->isEmpty())
                    <div class="p-6">
                        <div class="bg-warning-50 text-warning-700 p-4 rounded-lg text-center">
                            <span class="material-icons-outlined text-2xl">warning</span>
                            <p class="mt-2">{{ __('Add API credentials first') }}</p>
                            <a href="{{ route('ripe.credentials') }}" class="mt-2 inline-block text-warning-800 underline">{{ __('Add Credential') }}</a>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('ripe.subnet.routes.create', $subnet) }}" method="POST" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <label for="credential_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('API Credential') }} *</label>
                            <select name="credential_id" id="credential_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">{{ __('Select...') }}</option>
                                @foreach($credentials as $credential)
                                <option value="{{ $credential->id }}">{{ $credential->name }}</option>
                                @endforeach
                            </select>
                            @error('credential_id')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Route (Prefix)') }}</label>
                            <input type="text" disabled
                                   class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50"
                                   value="{{ $subnet->ip_address }}/{{ $subnet->cidr }}">
                        </div>

                        <div>
                            <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Origin AS') }} *</label>
                            <input type="text" name="origin" id="origin" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   pattern="AS\d+"
                                   placeholder="AS12345"
                                   value="{{ old('origin') }}">
                            <p class="text-gray-500 text-xs mt-1">{{ __('The AS number that will announce this prefix') }}</p>
                            @error('origin')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="descr" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                            <input type="text" name="descr" id="descr"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   value="{{ old('descr') }}"
                                   placeholder="{{ __('Route description') }}">
                            @error('descr')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Remarks') }}</label>
                            <textarea name="remarks" id="remarks" rows="2"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="{{ __('Optional remarks...') }}">{{ old('remarks') }}</textarea>
                            @error('remarks')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">add</span>
                            {{ __('Create Route Object') }}
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-info-50 rounded-xl p-6">
                    <h4 class="font-semibold text-info-800 mb-3">{{ __('About Route Objects') }}</h4>
                    <ul class="text-info-700 text-sm space-y-2">
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">info</span>
                            {{ __('Route objects authorize an AS to announce a prefix') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">info</span>
                            {{ __('Required for proper BGP routing') }}
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-info-500 mr-2 text-sm">info</span>
                            {{ __('Consider also creating ROAs for RPKI validation') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
