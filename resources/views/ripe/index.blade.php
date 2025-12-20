<x-app-layout>
    <x-slot name="header">{{ __('RIPE Management') }}</x-slot>
    <x-slot name="title">{{ __('RIPE Management') }}</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('RIPE Management') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Manage your RIPE database objects, geolocation, and routing information') }}</p>
            </div>
            <a href="{{ route('ripe.credentials') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                <span class="material-icons-outlined mr-2">key</span>
                {{ __('Manage Credentials') }}
            </a>
        </div>

        @if(!$company)
        <!-- No Company Warning -->
        <div class="bg-warning-50 border-l-4 border-warning-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <span class="material-icons-outlined text-warning-500 mr-3">warning</span>
                <div>
                    <p class="text-warning-700 font-medium">{{ __('Company Required') }}</p>
                    <p class="text-warning-600 text-sm">{{ __('You need to create a company profile to use RIPE management features.') }}</p>
                </div>
            </div>
            <a href="{{ route('company.create') }}" class="mt-3 inline-block bg-warning-600 text-white px-4 py-2 rounded-lg hover:bg-warning-700">
                {{ __('Create Company') }}
            </a>
        </div>
        @else

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-primary-600">key</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $credentials->count() }}</p>
                        <p class="text-gray-500 text-sm">{{ __('API Credentials') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-success-600">lan</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $subnets->count() }}</p>
                        <p class="text-gray-500 text-sm">{{ __('Linked Subnets') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-secondary-600">business</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-bold text-gray-800 truncate">{{ $company->ripe_org_id ?? '-' }}</p>
                        <p class="text-gray-500 text-sm">{{ __('RIPE Org ID') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-info-600">shield</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-bold text-gray-800 truncate">{{ $company->ripe_default_maintainer ?? '-' }}</p>
                        <p class="text-gray-500 text-sm">{{ __('Default Maintainer') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credentials Section -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('RIPE API Credentials') }}</h3>
                <p class="text-gray-500 text-sm mt-1">{{ __('Credentials for accessing the RIPE Database REST API') }}</p>
            </div>
            <div class="p-6">
                @if($credentials->isEmpty())
                <div class="text-center py-8">
                    <span class="material-icons-outlined text-6xl text-gray-300">vpn_key</span>
                    <p class="mt-4 text-gray-500">{{ __('No API credentials configured') }}</p>
                    <a href="{{ route('ripe.credentials') }}" class="mt-4 inline-block bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
                        {{ __('Add Credential') }}
                    </a>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Name') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Maintainer') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Status') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Expires') }}</th>
                                <th class="text-right py-3 px-4 text-gray-500 font-medium">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credentials as $credential)
                            <tr class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <span class="font-medium text-gray-800">{{ $credential->name }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $credential->maintainer ?? '-' }}</code>
                                </td>
                                <td class="py-3 px-4">
                                    @if($credential->isValid())
                                    <span class="px-2 py-1 text-xs bg-success-100 text-success-700 rounded-full">{{ __('Active') }}</span>
                                    @else
                                    <span class="px-2 py-1 text-xs bg-danger-100 text-danger-700 rounded-full">{{ __('Expired') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-sm">
                                    {{ $credential->expires_at ? $credential->expires_at->format('Y-m-d') : __('Never') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('ripe.credentials.destroy', $credential) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 hover:text-danger-800">
                                            <span class="material-icons-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <!-- Linked Subnets Section -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('RIPE-Linked Subnets') }}</h3>
                <p class="text-gray-500 text-sm mt-1">{{ __('Subnets with RIPE database objects') }}</p>
            </div>
            <div class="p-6">
                @if($subnets->isEmpty())
                <div class="text-center py-8">
                    <span class="material-icons-outlined text-6xl text-gray-300">router</span>
                    <p class="mt-4 text-gray-500">{{ __('No subnets linked to RIPE yet') }}</p>
                    <p class="text-gray-400 text-sm">{{ __('Link your subnets to manage WHOIS and routing objects') }}</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Subnet') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('RIPE Netname') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Status') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Last Synced') }}</th>
                                <th class="text-right py-3 px-4 text-gray-500 font-medium">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subnets as $subnet)
                            <tr class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <span class="font-mono font-medium text-gray-800">{{ $subnet->cidr_notation }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $subnet->ripe_netname ?? '-' }}</code>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-600">{{ $subnet->ripe_status ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-sm">
                                    {{ $subnet->ripe_last_synced_at ? $subnet->ripe_last_synced_at->diffForHumans() : __('Never') }}
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('ripe.subnet.info', $subnet) }}" class="text-primary-600 hover:text-primary-800" title="{{ __('View Info') }}">
                                        <span class="material-icons-outlined text-sm">info</span>
                                    </a>
                                    <a href="{{ route('ripe.subnet.edit', $subnet) }}" class="text-secondary-600 hover:text-secondary-800" title="{{ __('Edit') }}">
                                        <span class="material-icons-outlined text-sm">edit</span>
                                    </a>
                                    <a href="{{ route('ripe.subnet.routes', $subnet) }}" class="text-info-600 hover:text-info-800" title="{{ __('Route Objects') }}">
                                        <span class="material-icons-outlined text-sm">alt_route</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('ripe.credentials') }}" class="bg-white rounded-xl shadow-material-1 p-6 hover:shadow-material-2 transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-primary-600">add_circle</span>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-800">{{ __('Add API Credential') }}</p>
                        <p class="text-gray-500 text-sm">{{ __('Connect to RIPE Database API') }}</p>
                    </div>
                </div>
            </a>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-secondary-600">public</span>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-800">{{ __('RIPE Database') }}</p>
                        <a href="https://apps.db.ripe.net/" target="_blank" class="text-primary-600 hover:underline text-sm">{{ __('Open RIPE Portal') }} &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-info-600">analytics</span>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-800">{{ __('RIPEstat') }}</p>
                        <a href="https://stat.ripe.net/" target="_blank" class="text-primary-600 hover:underline text-sm">{{ __('Open RIPEstat') }} &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
