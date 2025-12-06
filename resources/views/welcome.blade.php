<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Soltia IPS Marketplace') }} - {{ __('IPv4 Address Leasing') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <span class="text-2xl font-bold text-primary-600">Soltia</span>
                        <span class="ml-2 text-xs text-gray-400 uppercase tracking-widest">IPS Marketplace</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-primary-600 transition-colors">{{ __('Features') }}</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-primary-600 transition-colors">{{ __('How It Works') }}</a>
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

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-white to-secondary-50"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-primary-100/50 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                        {{ __('The Modern Way to') }}
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-secondary-600">
                            {{ __('Lease IPv4') }}
                        </span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 leading-relaxed">
                        {{ __('Connect IP holders with businesses that need them. Our marketplace makes leasing IPv4 addresses simple, secure, and transparent.') }}
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-primary-600 text-white px-8 py-4 rounded-xl hover:bg-primary-700 transition-all font-semibold text-lg shadow-lg shadow-primary-500/30">
                            <span class="material-icons-outlined mr-2">rocket_launch</span>
                            {{ __('Start Leasing') }}
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center justify-center bg-white text-gray-700 px-8 py-4 rounded-xl hover:bg-gray-50 transition-all font-semibold text-lg border border-gray-200">
                            <span class="material-icons-outlined mr-2">store</span>
                            {{ __('Browse IPs') }}
                        </a>
                    </div>
                    <div class="mt-12 flex items-center space-x-8 text-sm text-gray-500">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-success-500 mr-2">verified</span>
                            {{ __('RIR Verified') }}
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-success-500 mr-2">security</span>
                            {{ __('Secure Transfers') }}
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-success-500 mr-2">support_agent</span>
                            {{ __('24/7 Support') }}
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-500 text-sm">{{ __('Available Now') }}</span>
                            <span class="bg-success-100 text-success-700 px-3 py-1 rounded-full text-sm font-medium">{{ __('Live') }}</span>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-mono font-bold text-lg text-primary-800">185.156.0.0/22</p>
                                        <p class="text-sm text-primary-600">RIPE NCC - Netherlands</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-secondary-600">$0.45/IP</p>
                                        <p class="text-sm text-gray-500">1,024 IPs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-mono font-bold text-lg text-gray-800">104.233.64.0/24</p>
                                        <p class="text-sm text-gray-600">ARIN - United States</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-secondary-600">$0.55/IP</p>
                                        <p class="text-sm text-gray-500">256 IPs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-mono font-bold text-lg text-gray-800">45.89.176.0/23</p>
                                        <p class="text-sm text-gray-600">RIPE NCC - Germany</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-secondary-600">$0.50/IP</p>
                                        <p class="text-sm text-gray-500">512 IPs</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900">{{ __('Why Choose Soltia?') }}</h2>
                <p class="mt-4 text-xl text-gray-600">{{ __('Everything you need to manage IP addresses efficiently') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-primary-50 to-white rounded-2xl p-8 border border-primary-100">
                    <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons-outlined text-primary-600 text-2xl">verified_user</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Verified Ownership') }}</h3>
                    <p class="text-gray-600">{{ __('All IP blocks are verified through RIR databases to ensure legitimate ownership.') }}</p>
                </div>

                <div class="bg-gradient-to-br from-secondary-50 to-white rounded-2xl p-8 border border-secondary-100">
                    <div class="w-14 h-14 bg-secondary-100 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons-outlined text-secondary-600 text-2xl">speed</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Instant Deployment') }}</h3>
                    <p class="text-gray-600">{{ __('Get your LOA and start using IP addresses within hours, not days or weeks.') }}</p>
                </div>

                <div class="bg-gradient-to-br from-success-50 to-white rounded-2xl p-8 border border-success-100">
                    <div class="w-14 h-14 bg-success-100 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons-outlined text-success-600 text-2xl">shield</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Clean Reputation') }}</h3>
                    <p class="text-gray-600">{{ __('All IPs are checked against major blocklists to ensure clean reputation.') }}</p>
                </div>

                <div class="bg-gradient-to-br from-warning-50 to-white rounded-2xl p-8 border border-warning-100">
                    <div class="w-14 h-14 bg-warning-100 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons-outlined text-warning-600 text-2xl">payments</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Flexible Billing') }}</h3>
                    <p class="text-gray-600">{{ __('Monthly billing with no long-term commitments. Scale up or down as needed.') }}</p>
                </div>

                <div class="bg-gradient-to-br from-danger-50 to-white rounded-2xl p-8 border border-danger-100">
                    <div class="w-14 h-14 bg-danger-100 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons-outlined text-danger-600 text-2xl">gavel</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Legal Protection') }}</h3>
                    <p class="text-gray-600">{{ __('Automated LOA generation and abuse management to protect all parties.') }}</p>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons-outlined text-gray-600 text-2xl">analytics</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Full Transparency') }}</h3>
                    <p class="text-gray-600">{{ __('Real-time dashboards, detailed invoices, and complete transaction history.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900">{{ __('How It Works') }}</h2>
                <p class="mt-4 text-xl text-gray-600">{{ __('Get started in three simple steps') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Create Account') }}</h3>
                    <p class="text-gray-600">{{ __('Sign up and complete your company verification in minutes.') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Choose IPs') }}</h3>
                    <p class="text-gray-600">{{ __('Browse available subnets and select the ones that fit your needs.') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('Start Using') }}</h3>
                    <p class="text-gray-600">{{ __('Receive your LOA and configure your ASN to start routing.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">{{ __('Ready to Get Started?') }}</h2>
            <p class="text-xl text-primary-100 mb-8">{{ __('Join hundreds of companies already using Soltia for their IP needs.') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-white text-primary-600 px-8 py-4 rounded-xl hover:bg-primary-50 transition-all font-semibold text-lg">
                    {{ __('Create Free Account') }}
                </a>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center justify-center bg-primary-500 text-white px-8 py-4 rounded-xl hover:bg-primary-400 transition-all font-semibold text-lg border border-primary-400">
                    {{ __('View Available IPs') }}
                </a>
            </div>
        </div>
    </section>

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
                        <li><a href="#features" class="hover:text-white transition-colors">{{ __('Features') }}</a></li>
                        <li><a href="{{ route('help.index') }}" class="hover:text-white transition-colors">{{ __('Help Center') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('Company') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('About') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Blog') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('Legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Privacy') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Terms') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Acceptable Use') }}</a></li>
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
