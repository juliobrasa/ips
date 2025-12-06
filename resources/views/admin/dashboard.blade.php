<x-admin-layout>
    <x-slot name="header">Admin Dashboard</x-slot>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Users -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-primary-600">people</span>
                </div>
            </div>
        </div>

        <!-- Total Companies -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Companies</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_companies']) }}</p>
                </div>
                <div class="w-12 h-12 bg-secondary-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-secondary-600">business</span>
                </div>
            </div>
        </div>

        <!-- Pending KYC -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending KYC</p>
                    <p class="text-3xl font-bold text-warning-600">{{ number_format($stats['pending_kyc'] + $stats['in_review_kyc']) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">pending_actions</span>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                {{ $stats['pending_kyc'] }} pending, {{ $stats['in_review_kyc'] }} in review
            </div>
        </div>

        <!-- Active Leases -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Active Leases</p>
                    <p class="text-3xl font-bold text-success-600">{{ number_format($stats['active_leases']) }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">assignment_turned_in</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Subnets -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Subnets</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_subnets']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-gray-600">lan</span>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                {{ $stats['available_subnets'] }} available, {{ $stats['pending_subnets'] }} pending
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold text-success-600">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-success-600">attach_money</span>
                </div>
            </div>
        </div>

        <!-- Pending Payouts -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Payouts</p>
                    <p class="text-3xl font-bold text-warning-600">${{ number_format($stats['pending_payouts'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-warning-600">schedule_send</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent KYC Requests -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Pending KYC Requests</h3>
                <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    View all
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentKycRequests as $company)
                <a href="{{ route('admin.kyc.review', $company) }}" class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="material-icons-outlined text-primary-600 text-sm">business</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $company->company_name }}</p>
                        <p class="text-sm text-gray-500">{{ $company->user->email }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 text-xs rounded-full
                            {{ $company->kyc_status === 'pending' ? 'bg-warning-100 text-warning-700' : '' }}
                            {{ $company->kyc_status === 'in_review' ? 'bg-primary-100 text-primary-700' : '' }}
                        ">
                            {{ ucfirst(str_replace('_', ' ', $company->kyc_status)) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $company->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <span class="material-icons-outlined text-4xl mb-2">check_circle</span>
                    <p>No pending KYC requests</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Subnet Verifications -->
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Pending Subnet Verifications</h3>
                <a href="{{ route('admin.subnets.index', ['status' => 'pending_verification']) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    View all
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentSubnets as $subnet)
                <a href="{{ route('admin.subnets.show', $subnet) }}" class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="material-icons-outlined text-secondary-600 text-sm">router</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800 font-mono">{{ $subnet->cidr_notation }}</p>
                        <p class="text-sm text-gray-500">{{ $subnet->company->company_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-warning-100 text-warning-700">
                            {{ $subnet->rir }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $subnet->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <span class="material-icons-outlined text-4xl mb-2">check_circle</span>
                    <p>No pending subnet verifications</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 bg-white rounded-xl shadow-material-1 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">verified_user</span>
                <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">Review KYC</span>
            </a>
            <a href="{{ route('admin.subnets.index', ['status' => 'pending_verification']) }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">dns</span>
                <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">Verify Subnets</span>
            </a>
            <a href="{{ route('admin.finance.payouts', ['status' => 'pending']) }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">payments</span>
                <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">Process Payouts</span>
            </a>
            <a href="{{ route('admin.finance.revenue') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                <span class="material-icons-outlined text-3xl text-gray-400 group-hover:text-primary-600 mb-2">analytics</span>
                <span class="text-sm font-medium text-gray-600 group-hover:text-primary-600">View Reports</span>
            </a>
        </div>
    </div>
</x-admin-layout>
