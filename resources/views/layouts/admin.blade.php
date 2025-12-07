<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - Soltia IPS Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">

        <!-- Admin Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
               :class="{ '-translate-x-full': !mobileMenuOpen, 'translate-x-0': mobileMenuOpen }"
               @click.away="mobileMenuOpen = false">

            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <span class="material-icons-outlined text-3xl text-primary-400">admin_panel_settings</span>
                    <span class="text-xl font-bold">{{ __('Admin Panel') }}</span>
                </a>
            </div>

            <!-- Language Switcher -->
            <div class="px-4 py-2 bg-gray-800 border-t border-gray-700">
                <div class="flex items-center justify-center space-x-2">
                    <a href="{{ route('language.switch', 'en') }}" class="px-2 py-1 text-xs rounded {{ app()->getLocale() === 'en' ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white' }}">EN</a>
                    <a href="{{ route('language.switch', 'es') }}" class="px-2 py-1 text-xs rounded {{ app()->getLocale() === 'es' ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white' }}">ES</a>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-3">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">dashboard</span>
                        {{ __('Dashboard') }}
                    </a>

                    <!-- User Management -->
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">people</span>
                        {{ __('Users') }}
                    </a>

                    <!-- KYC Management -->
                    <a href="{{ route('admin.kyc.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.kyc.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">verified_user</span>
                        {{ __('KYC Management') }}
                        @php $pendingKyc = \App\Models\Company::whereIn('kyc_status', ['pending', 'in_review'])->count(); @endphp
                        @if($pendingKyc > 0)
                        <span class="ml-auto bg-warning-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingKyc }}</span>
                        @endif
                    </a>

                    <!-- Subnet Management -->
                    <a href="{{ route('admin.subnets.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.subnets.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">lan</span>
                        {{ __('Subnets') }}
                    </a>

                    <!-- Lease Management -->
                    <a href="{{ route('admin.leases.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.leases.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">assignment</span>
                        {{ __('Leases') }}
                    </a>

                    <!-- Finance Section -->
                    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ __('Finance') }}
                    </div>

                    <a href="{{ route('admin.finance.invoices') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.finance.invoices') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">receipt_long</span>
                        {{ __('Invoices') }}
                    </a>

                    <a href="{{ route('admin.finance.payouts') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.finance.payouts*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">payments</span>
                        {{ __('Payouts') }}
                        @php $pendingPayouts = \App\Models\Payout::where('status', 'pending')->count(); @endphp
                        @if($pendingPayouts > 0)
                        <span class="ml-auto bg-primary-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingPayouts }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.finance.revenue') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.finance.revenue') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">analytics</span>
                        {{ __('Revenue Report') }}
                    </a>

                    <!-- Security & Tools Section -->
                    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ __('Security & Tools') }}
                    </div>

                    <a href="{{ route('admin.security.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.security.index') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">security</span>
                        {{ __('Security Dashboard') }}
                        @php $openReports = \App\Models\AbuseReport::where('status', 'open')->count(); @endphp
                        @if($openReports > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $openReports }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.security.blocklist-check') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.security.blocklist-check') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">manage_search</span>
                        {{ __('Blocklist Checker') }}
                    </a>

                    <a href="{{ route('admin.security.abuse-reports') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.security.abuse-reports*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">flag</span>
                        {{ __('Abuse Reports') }}
                    </a>

                    <a href="{{ route('admin.documents.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.documents.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <span class="material-icons-outlined mr-3">description</span>
                        {{ __('Document Templates') }}
                    </a>
                </div>

                <!-- Divider -->
                <div class="my-6 border-t border-gray-700"></div>

                <!-- Back to App -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors text-gray-300 hover:bg-gray-700">
                    <span class="material-icons-outlined mr-3">arrow_back</span>
                    {{ __('Back to App') }}
                </a>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-800">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center">
                        <span class="text-white font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ __('Administrator') }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-material-1 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-600">
                    <span class="material-icons-outlined">menu</span>
                </button>

                <!-- Page Title -->
                <h1 class="text-xl font-semibold text-gray-800 hidden lg:block">
                    @isset($header)
                        {{ $header }}
                    @else
                        {{ __('Admin Dashboard') }}
                    @endisset
                </h1>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 text-xs font-medium bg-danger-100 text-danger-700 rounded-full">
                        {{ __('Admin Mode') }}
                    </span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-6 bg-success-50 border-l-4 border-success-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-success-500 mr-3">check_circle</span>
                            <p class="text-success-700">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-success-500 hover:text-success-700">
                            <span class="material-icons-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-danger-50 border-l-4 border-danger-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-danger-500 mr-3">error</span>
                            <p class="text-danger-700">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-danger-500 hover:text-danger-700">
                            <span class="material-icons-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('warning'))
                <div class="mb-6 bg-warning-50 border-l-4 border-warning-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-warning-500 mr-3">warning</span>
                            <p class="text-warning-700">{{ session('warning') }}</p>
                        </div>
                        <button @click="show = false" class="text-warning-500 hover:text-warning-700">
                            <span class="material-icons-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
