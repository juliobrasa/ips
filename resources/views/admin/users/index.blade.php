<x-admin-layout>
    <x-slot name="header">{{ __('User Management') }}</x-slot>
    <x-slot name="title">{{ __('User Management') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Total Users') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-gray-600">people</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Active') }}</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Administrators') }}</p>
                    <p class="text-2xl font-bold text-primary-600">{{ $stats['admins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">admin_panel_settings</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Email Verified') }}</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['verified'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-blue-600">verified</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white rounded-xl shadow-material-1 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end justify-between">
            <form method="GET" class="flex flex-wrap gap-4 items-end flex-1">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="{{ __('Name, email or company...') }}">
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Role') }}</label>
                    <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="all">{{ __('All Roles') }}</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="all">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                    </select>
                </div>

                <!-- KYC Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('KYC') }}</label>
                    <select name="kyc" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="all">{{ __('All KYC') }}</option>
                        <option value="pending" {{ request('kyc') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="in_review" {{ request('kyc') == 'in_review' ? 'selected' : '' }}>{{ __('In Review') }}</option>
                        <option value="approved" {{ request('kyc') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="rejected" {{ request('kyc') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <span class="material-icons-outlined align-middle mr-1">search</span>
                    {{ __('Filter') }}
                </button>
            </form>

            <a href="{{ route('admin.users.create') }}"
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <span class="material-icons-outlined mr-2">person_add</span>
                {{ __('Add User') }}
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('User') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Company') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Role') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('KYC') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Registered') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            @if($user->email_verified_at)
                            <span class="ml-2 text-green-500" title="{{ __('Email Verified') }}">
                                <span class="material-icons-outlined text-sm">verified</span>
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->company)
                            <p class="text-sm font-medium text-gray-900">{{ $user->company->company_name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->company->country }}</p>
                        @else
                            <span class="text-gray-400">{{ __('No company') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($user->role === 'admin')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="material-icons-outlined text-xs mr-1">admin_panel_settings</span>
                                {{ __('Admin') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ __('User') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-gray-100 text-gray-800',
                                'suspended' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$user->status ?? 'active'] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->company)
                            @php
                                $kycColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_review' => 'bg-blue-100 text-blue-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kycColors[$user->company->kyc_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $user->company->kyc_status)) }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $user->created_at->format('Y-m-d') }}</span>
                        <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
                               title="{{ __('View') }}">
                                <span class="material-icons-outlined text-lg">visibility</span>
                            </a>

                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
                               title="{{ __('Edit') }}">
                                <span class="material-icons-outlined text-lg">edit</span>
                            </a>

                            @if($user->id !== auth()->id())
                                @if($user->status !== 'suspended')
                                <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 text-yellow-500 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg transition-colors"
                                            title="{{ __('Suspend') }}"
                                            onclick="return confirm('{{ __('Are you sure you want to suspend this user?') }}')">
                                        <span class="material-icons-outlined text-lg">block</span>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                            title="{{ __('Activate') }}">
                                        <span class="material-icons-outlined text-lg">check_circle</span>
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors"
                                            title="{{ __('Impersonate') }}">
                                        <span class="material-icons-outlined text-lg">login</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-icons-outlined text-4xl mb-2">people</span>
                        <p>{{ __('No users found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>
