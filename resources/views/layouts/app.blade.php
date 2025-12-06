<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('Dashboard') }} - {{ __('Soltia IPS Marketplace') }}</title>

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

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-800 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
               :class="{ '-translate-x-full': !mobileMenuOpen, 'translate-x-0': mobileMenuOpen }"
               @click.away="mobileMenuOpen = false">

            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-primary-900">
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2">
                    <span class="material-icons-outlined text-3xl text-secondary-400">hub</span>
                    <span class="text-xl font-bold">{{ __('Soltia IPS Marketplace') }}</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    @auth
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">dashboard</span>
                        {{ __('Dashboard') }}
                    </a>
                    @endauth

                    <!-- Marketplace -->
                    <a href="{{ route('marketplace.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('marketplace.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">store</span>
                        {{ __('Marketplace') }}
                    </a>

                    @auth
                    @if(auth()->user()->company?->isLessee())
                    <!-- My Leases (Lessee) -->
                    <a href="{{ route('leases.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('leases.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">assignment</span>
                        {{ __('My Leases') }}
                    </a>
                    @endif

                    @if(auth()->user()->company?->isHolder())
                    <!-- My Subnets (Holder) -->
                    <a href="{{ route('subnets.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('subnets.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">lan</span>
                        {{ __('My Subnets') }}
                    </a>

                    <!-- Payouts -->
                    <a href="{{ route('payouts.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('payouts.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">payments</span>
                        {{ __('Payouts') }}
                    </a>
                    @endif

                    <!-- Invoices -->
                    <a href="{{ route('invoices.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('invoices.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">receipt_long</span>
                        {{ __('Invoices') }}
                    </a>
                    @endauth
                </div>

                @auth
                <!-- Admin Link -->
                @if(auth()->user()->role === 'admin')
                <div class="my-6 border-t border-primary-700"></div>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors bg-danger-600 text-white hover:bg-danger-700">
                        <span class="material-icons-outlined mr-3">admin_panel_settings</span>
                        {{ __('Admin Panel') }}
                    </a>
                </div>
                @endif

                <!-- Divider -->
                <div class="my-6 border-t border-primary-700"></div>

                <!-- Settings Section -->
                <div class="space-y-1">
                    <a href="{{ route('company.edit') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('company.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">business</span>
                        {{ __('Company Profile') }}
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('profile.*') && !request()->routeIs('kyc.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">person</span>
                        {{ __('My Profile') }}
                    </a>

                    <a href="{{ route('kyc.documents') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('kyc.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <span class="material-icons-outlined mr-3">verified_user</span>
                        {{ __('KYC Documents') }}
                        @if(auth()->user()->company && !auth()->user()->company->isKycApproved())
                            <span class="ml-auto w-2 h-2 bg-warning-500 rounded-full"></span>
                        @endif
                    </a>
                </div>
                @else
                <!-- Guest Links -->
                <div class="my-6 border-t border-primary-700"></div>
                <div class="space-y-1">
                    <a href="{{ route('login') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors text-primary-100 hover:bg-primary-700">
                        <span class="material-icons-outlined mr-3">login</span>
                        {{ __('Sign In') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-colors text-primary-100 hover:bg-primary-700">
                        <span class="material-icons-outlined mr-3">person_add</span>
                        {{ __('Register') }}
                    </a>
                </div>
                @endauth
            </nav>

            <!-- User Info at Bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-primary-900">
                @auth
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-secondary-500 flex items-center justify-center">
                        <span class="text-white font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-primary-300">{{ auth()->user()->company?->company_name ?? __('No company') }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-primary-300 hover:text-white">
                            <span class="material-icons-outlined">logout</span>
                        </button>
                    </form>
                </div>
                @else
                <div class="text-center">
                    <p class="text-primary-300 text-sm">{{ __('Welcome to Soltia IPS Marketplace') }}</p>
                    <a href="{{ route('register') }}" class="text-secondary-400 hover:text-secondary-300 text-sm font-medium">{{ __('Create an account') }}</a>
                </div>
                @endauth
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
                        Dashboard
                    @endisset
                </h1>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <!-- Language Selector -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-primary-600">
                            <span class="material-icons-outlined">language</span>
                            <span class="ml-1 text-sm font-medium uppercase">{{ app()->getLocale() }}</span>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg py-1 z-50">
                            <a href="{{ route('language.switch', 'en') }}"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'en' ? 'bg-primary-50 text-primary-600' : '' }}">
                                <span class="mr-2">EN</span> {{ __('English') }}
                            </a>
                            <a href="{{ route('language.switch', 'es') }}"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'es' ? 'bg-primary-50 text-primary-600' : '' }}">
                                <span class="mr-2">ES</span> {{ __('Spanish') }}
                            </a>
                        </div>
                    </div>

                    @auth
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-primary-600">
                        <span class="material-icons-outlined">shopping_cart</span>
                        @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-danger-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>

                    <!-- Notifications -->
                    <button class="text-gray-600 hover:text-primary-600">
                        <span class="material-icons-outlined">notifications</span>
                    </button>

                    <!-- KYC Status Badge -->
                    @if(auth()->user()->company)
                        @if(auth()->user()->company->kyc_status === 'approved')
                            <span class="px-3 py-1 text-xs font-medium bg-success-100 text-success-700 rounded-full">
                                {{ __('KYC Verified') }}
                            </span>
                        @elseif(auth()->user()->company->kyc_status === 'pending')
                            <span class="px-3 py-1 text-xs font-medium bg-warning-100 text-warning-700 rounded-full">
                                {{ __('KYC Pending') }}
                            </span>
                        @elseif(auth()->user()->company->kyc_status === 'in_review')
                            <span class="px-3 py-1 text-xs font-medium bg-primary-100 text-primary-700 rounded-full">
                                {{ __('KYC In Review') }}
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium bg-danger-100 text-danger-700 rounded-full">
                                {{ __('KYC Rejected') }}
                            </span>
                        @endif
                    @else
                        <a href="{{ route('company.create') }}" class="px-3 py-1 text-xs font-medium bg-warning-100 text-warning-700 rounded-full hover:bg-warning-200">
                            {{ __('Complete Profile') }}
                        </a>
                    @endif
                    @else
                    <!-- Guest Links -->
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-600 font-medium">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 font-medium">{{ __('Get Started') }}</a>
                    @endauth
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

                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} {{ __('Soltia IPS Marketplace') }}. {{ __('All rights reserved.') }}</p>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-primary-600">{{ __('Terms') }}</a>
                        <a href="#" class="hover:text-primary-600">{{ __('Privacy') }}</a>
                        <a href="#" class="hover:text-primary-600">{{ __('Support') }}</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
