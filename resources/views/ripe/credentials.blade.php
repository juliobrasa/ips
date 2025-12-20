<x-app-layout>
    <x-slot name="header">{{ __('RIPE Credentials') }}</x-slot>
    <x-slot name="title">{{ __('RIPE Credentials') }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('ripe.index') }}" class="hover:text-primary-600">{{ __('RIPE Management') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('Credentials') }}</span>
        </div>

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('API Credentials') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Manage your RIPE Database API keys') }}</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-info-50 border-l-4 border-info-500 p-4 rounded-r-lg">
            <div class="flex">
                <span class="material-icons-outlined text-info-500 mr-3">info</span>
                <div>
                    <p class="text-info-700 font-medium">{{ __('How to get an API Key') }}</p>
                    <ol class="text-info-600 text-sm mt-2 list-decimal list-inside space-y-1">
                        <li>{{ __('Go to') }} <a href="https://apps.db.ripe.net/db-web-ui/api-keys" target="_blank" class="underline">RIPE Database API Keys</a></li>
                        <li>{{ __('Log in with your RIPE NCC Access account') }}</li>
                        <li>{{ __('Create a new API key and copy it here') }}</li>
                        <li>{{ __('Ensure your account is linked to the maintainer using an SSO auth attribute') }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add Credential Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-material-1">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Add New Credential') }}</h3>
                    </div>
                    <form action="{{ route('ripe.credentials.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} *</label>
                            <input type="text" name="name" id="name" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="{{ __('e.g., Production API Key') }}"
                                   value="{{ old('name') }}">
                            @error('name')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">{{ __('API Key') }} *</label>
                            <input type="password" name="api_key" id="api_key" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="{{ __('Paste your RIPE API key') }}">
                            @error('api_key')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="maintainer" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Default Maintainer') }}</label>
                            <input type="text" name="maintainer" id="maintainer"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="{{ __('e.g., YOUR-MNT') }}"
                                   value="{{ old('maintainer') }}">
                            <p class="text-gray-500 text-xs mt-1">{{ __('The maintainer object to use for updates') }}</p>
                            @error('maintainer')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Expiration Date') }}</label>
                            <input type="date" name="expires_at" id="expires_at"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   value="{{ old('expires_at') }}">
                            <p class="text-gray-500 text-xs mt-1">{{ __('RIPE API keys expire after 1 year') }}</p>
                            @error('expires_at')
                            <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">add</span>
                            {{ __('Add Credential') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Existing Credentials -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-material-1">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Existing Credentials') }}</h3>
                    </div>
                    <div class="p-6">
                        @if($credentials->isEmpty())
                        <div class="text-center py-8">
                            <span class="material-icons-outlined text-6xl text-gray-300">vpn_key</span>
                            <p class="mt-4 text-gray-500">{{ __('No credentials added yet') }}</p>
                            <p class="text-gray-400 text-sm">{{ __('Add your first RIPE API key to get started') }}</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($credentials as $credential)
                            <div class="border border-gray-200 rounded-lg p-4 {{ $credential->isValid() ? 'bg-white' : 'bg-gray-50' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="material-icons-outlined text-gray-400 mr-2">key</span>
                                            <h4 class="font-semibold text-gray-800">{{ $credential->name }}</h4>
                                            @if($credential->isValid())
                                            <span class="ml-2 px-2 py-0.5 text-xs bg-success-100 text-success-700 rounded-full">{{ __('Active') }}</span>
                                            @else
                                            <span class="ml-2 px-2 py-0.5 text-xs bg-danger-100 text-danger-700 rounded-full">{{ __('Expired') }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">{{ __('Maintainer:') }}</span>
                                                <code class="ml-1 bg-gray-100 px-2 py-0.5 rounded">{{ $credential->maintainer ?? '-' }}</code>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">{{ __('Expires:') }}</span>
                                                <span class="ml-1 {{ $credential->expires_at && $credential->expires_at->isPast() ? 'text-danger-600' : '' }}">
                                                    {{ $credential->expires_at ? $credential->expires_at->format('Y-m-d') : __('Never') }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">{{ __('Validated:') }}</span>
                                                <span class="ml-1">{{ $credential->validated_at ? $credential->validated_at->format('Y-m-d H:i') : __('Never') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">{{ __('Created:') }}</span>
                                                <span class="ml-1">{{ $credential->created_at->format('Y-m-d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('ripe.credentials.destroy', $credential) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this credential?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 hover:text-danger-800 p-2">
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Expiring Soon Warning -->
                @php $expiringSoon = $credentials->filter(fn($c) => $c->expires_at && $c->expires_at->diffInDays(now()) <= 30 && !$c->expires_at->isPast()); @endphp
                @if($expiringSoon->isNotEmpty())
                <div class="mt-4 bg-warning-50 border-l-4 border-warning-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <span class="material-icons-outlined text-warning-500 mr-3">schedule</span>
                        <div>
                            <p class="text-warning-700 font-medium">{{ __('Credentials Expiring Soon') }}</p>
                            <ul class="text-warning-600 text-sm mt-1">
                                @foreach($expiringSoon as $credential)
                                <li>"{{ $credential->name }}" {{ __('expires') }} {{ $credential->expires_at->diffForHumans() }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
