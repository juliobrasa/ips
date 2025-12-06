<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('Soltia IPS Marketplace') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <span class="text-2xl font-bold text-primary-600">Soltia</span>
                        <span class="ml-2 text-xs text-gray-400 uppercase tracking-widest">IPS Marketplace</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('marketplace.index') }}" class="text-gray-600 hover:text-primary-600 transition-colors">{{ __('Marketplace') }}</a>
                    <a href="{{ route('help.index') }}" class="text-gray-600 hover:text-primary-600 transition-colors">{{ __('Help') }}</a>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Language Selector -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-primary-600">
                            <span class="material-icons-outlined text-xl">language</span>
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
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-primary-600 transition-colors">{{ __('Dashboard') }}</a>
                    @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-600 transition-colors">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        {{ __('Get Started') }}
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-4">Soltia</h3>
                    <p class="text-sm">{{ __('The modern IPv4 marketplace for businesses of all sizes.') }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('Product') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('marketplace.index') }}" class="hover:text-white transition-colors">{{ __('Marketplace') }}</a></li>
                        <li><a href="{{ route('help.index') }}" class="hover:text-white transition-colors">{{ __('Help Center') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('Company') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('About') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('Legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Privacy') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Terms') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} {{ __('Soltia IPS Marketplace') }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
