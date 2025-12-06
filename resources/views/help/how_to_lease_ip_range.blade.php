<x-guest-layout>
    <x-slot name="title">{{ __('How to Lease an IP Range') }}</x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-secondary-600 to-secondary-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <a href="{{ route('help.index') }}" class="inline-flex items-center text-secondary-100 hover:text-white mb-4">
                <span class="material-icons-outlined mr-1">arrow_back</span>
                {{ __('Back to Help Center') }}
            </a>
            <h1 class="text-3xl font-bold">{{ __('How to Lease an IP Range') }}</h1>
            <p class="text-secondary-100 mt-2">{{ __('Step-by-step guide to finding and leasing IP addresses') }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Introduction -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ __('Overview') }}</h2>
            <p class="text-gray-600 mb-4">
                {{ __('Need additional IPv4 addresses for your business? Our marketplace connects you with verified IP holders who have clean, reputable IP ranges available for lease. This guide will walk you through the process of finding and leasing the perfect IP range for your needs.') }}
            </p>
            <div class="bg-secondary-50 border border-secondary-200 rounded-lg p-4">
                <div class="flex items-start">
                    <span class="material-icons-outlined text-secondary-600 mr-3">info</span>
                    <div>
                        <p class="text-secondary-800 font-medium">{{ __('What You Get') }}</p>
                        <ul class="text-secondary-700 text-sm mt-2 space-y-1">
                            <li>{{ __('Verified, clean IP addresses with good reputation') }}</li>
                            <li>{{ __('Letter of Authorization (LOA) for BGP announcements') }}</li>
                            <li>{{ __('Flexible lease terms (1 to 12 months)') }}</li>
                            <li>{{ __('24/7 access to your lease management dashboard') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-secondary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">1</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Create an Account and Complete KYC') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Before you can lease IP addresses, you need to register and verify your company:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Click "Register" and create your account with your email') }}</li>
                    <li>{{ __('Verify your email address by clicking the link sent to you') }}</li>
                    <li>{{ __('Complete your company profile:') }}
                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1 text-sm">
                            <li>{{ __('Company legal name') }}</li>
                            <li>{{ __('Tax ID / VAT number') }}</li>
                            <li>{{ __('Business address') }}</li>
                            <li>{{ __('Select "IP Lessee" as your account type') }}</li>
                        </ul>
                    </li>
                    <li>{{ __('Wait for KYC approval (typically 1-2 business days)') }}</li>
                </ol>

                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4 mt-4">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-primary-600 mr-3">lightbulb</span>
                        <p class="text-primary-800 text-sm">
                            <strong>{{ __('Tip:') }}</strong> {{ __('You can browse the marketplace while waiting for verification, but you will need an approved account to complete a lease.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-secondary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">2</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Browse the Marketplace') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Explore available IP ranges and find the perfect match for your needs:') }}</p>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-medium text-gray-800 mb-4">{{ __('Filter Options') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start">
                            <span class="material-icons-outlined text-secondary-600 mr-3">public</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ __('By RIR') }}</p>
                                <p class="text-sm">{{ __('RIPE (Europe), ARIN (North America), LACNIC (Latin America), APNIC (Asia Pacific), AFRINIC (Africa)') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="material-icons-outlined text-secondary-600 mr-3">location_on</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ __('By Country') }}</p>
                                <p class="text-sm">{{ __('Filter by IP geolocation country') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="material-icons-outlined text-secondary-600 mr-3">straighten</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ __('By Size (CIDR)') }}</p>
                                <p class="text-sm">{{ __('/24 (256 IPs), /23 (512 IPs), /22 (1024 IPs), etc.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="material-icons-outlined text-secondary-600 mr-3">attach_money</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ __('By Price') }}</p>
                                <p class="text-sm">{{ __('Set min/max price per IP per month') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4 class="font-medium text-gray-800 mb-3">{{ __('What to Look For') }}</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-success-500 mr-2">verified</span>
                            <div>
                                <span class="font-medium">{{ __('Reputation Score') }}</span>
                                <p class="text-sm text-gray-500">{{ __('Higher scores (90+) mean cleaner IPs with less risk of blacklisting') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-success-500 mr-2">location_on</span>
                            <div>
                                <span class="font-medium">{{ __('Geolocation') }}</span>
                                <p class="text-sm text-gray-500">{{ __('Important if you need IPs from a specific region') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-success-500 mr-2">business</span>
                            <div>
                                <span class="font-medium">{{ __('Holder Verification') }}</span>
                                <p class="text-sm text-gray-500">{{ __('All holders are KYC verified for your protection') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-secondary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">3</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Add to Cart and Configure Lease') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('When you find a subnet you want to lease:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Click on the subnet to view its details') }}</li>
                    <li>{{ __('Review the subnet information:') }}
                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1 text-sm">
                            <li>{{ __('IP range and size') }}</li>
                            <li>{{ __('Reputation score and history') }}</li>
                            <li>{{ __('Geolocation details') }}</li>
                            <li>{{ __('Holder information') }}</li>
                        </ul>
                    </li>
                    <li>{{ __('Select your desired lease duration (1-12 months)') }}</li>
                    <li>{{ __('Click "Add to Cart"') }}</li>
                </ol>

                <div class="bg-gray-100 rounded-lg p-4 mt-4">
                    <h4 class="font-medium text-gray-800 mb-3">{{ __('Lease Duration Options') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">1</p>
                            <p class="text-sm text-gray-500">{{ __('month') }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">3</p>
                            <p class="text-sm text-gray-500">{{ __('months') }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center border-2 border-secondary-500">
                            <p class="text-lg font-bold text-secondary-600">6</p>
                            <p class="text-sm text-gray-500">{{ __('months') }}</p>
                            <span class="text-xs text-secondary-600">{{ __('Popular') }}</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">12</p>
                            <p class="text-sm text-gray-500">{{ __('months') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-secondary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">4</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Checkout and Payment') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Complete your order:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Go to your cart to review your selections') }}</li>
                    <li>{{ __('Verify the lease terms and total cost') }}</li>
                    <li>{{ __('Click "Proceed to Checkout"') }}</li>
                    <li>{{ __('Select your payment method:') }}
                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1 text-sm">
                            <li>{{ __('Credit/Debit Card') }}</li>
                            <li>{{ __('Bank Transfer') }}</li>
                        </ul>
                    </li>
                    <li>{{ __('Complete the payment') }}</li>
                </ol>

                <div class="bg-gray-50 rounded-lg p-6 mt-4">
                    <h4 class="font-medium text-gray-800 mb-3">{{ __('Cost Breakdown Example') }}</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>{{ __('/24 Subnet (256 IPs) × $0.50/IP × 3 months') }}</span>
                            <span class="font-medium">$384.00</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>{{ __('Tax (if applicable)') }}</span>
                            <span>$0.00</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-bold text-gray-800">
                            <span>{{ __('Total') }}</span>
                            <span>$384.00</span>
                        </div>
                    </div>
                </div>

                <div class="bg-success-50 border border-success-200 rounded-lg p-4 mt-4">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-success-600 mr-3">security</span>
                        <div>
                            <p class="text-success-800 font-medium">{{ __('Secure Payments') }}</p>
                            <p class="text-success-700 text-sm">{{ __('All payments are processed securely. Your payment information is encrypted and never stored on our servers.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-secondary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">5</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Configure Your ASN') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('After payment, you need to assign your Autonomous System Number (ASN) to the lease:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Go to "My Leases" in your dashboard') }}</li>
                    <li>{{ __('Click on your new lease') }}</li>
                    <li>{{ __('Enter your ASN number (e.g., AS12345)') }}</li>
                    <li>{{ __('Click "Assign ASN"') }}</li>
                </ol>

                <div class="bg-warning-50 border border-warning-200 rounded-lg p-4 mt-4">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-warning-600 mr-3">warning</span>
                        <div>
                            <p class="text-warning-800 font-medium">{{ __('Important') }}</p>
                            <p class="text-warning-700 text-sm">{{ __('You must have a valid ASN to announce the leased IP addresses via BGP. If you do not have an ASN, you will need to obtain one from your Regional Internet Registry or use your upstream provider\'s ASN with their permission.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-secondary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">6</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Download Your LOA') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('The Letter of Authorization (LOA) is your proof that you are authorized to announce the IP addresses:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('From your lease details page, click "Generate LOA"') }}</li>
                    <li>{{ __('Download the PDF document') }}</li>
                    <li>{{ __('Provide the LOA to your upstream providers or data centers') }}</li>
                </ol>

                <div class="bg-gray-50 rounded-lg p-6 mt-4">
                    <h4 class="font-medium text-gray-800 mb-3">{{ __('The LOA Contains') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2 text-lg">check_circle</span>
                            {{ __('IP range details (network address, CIDR)') }}
                        </li>
                        <li class="flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2 text-lg">check_circle</span>
                            {{ __('Your company and ASN information') }}
                        </li>
                        <li class="flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2 text-lg">check_circle</span>
                            {{ __('Lease validity period') }}
                        </li>
                        <li class="flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2 text-lg">check_circle</span>
                            {{ __('Verification code for authenticity') }}
                        </li>
                        <li class="flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2 text-lg">check_circle</span>
                            {{ __('IP holder authorization signature') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 7 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-success-600 text-white rounded-full flex items-center justify-center font-bold mr-4">7</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Start Using Your IPs') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('With your LOA in hand, you can now configure BGP routing:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Provide the LOA to your upstream provider(s)') }}</li>
                    <li>{{ __('Configure your router to announce the IP prefix') }}</li>
                    <li>{{ __('Verify the announcement using looking glass tools') }}</li>
                    <li>{{ __('Start using your new IP addresses!') }}</li>
                </ol>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2">router</span>
                            {{ __('BGP Configuration') }}
                        </h4>
                        <p class="text-sm">{{ __('Configure your border router to announce the leased prefix to your upstream providers.') }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <span class="material-icons-outlined text-secondary-600 mr-2">visibility</span>
                            {{ __('Verification') }}
                        </h4>
                        <p class="text-sm">{{ __('Use tools like bgp.he.net or RIPE RIS to verify your prefix is being announced correctly.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Managing Your Lease -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Managing Your Lease') }}</h2>

            <div class="space-y-6 text-gray-600">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <span class="material-icons-outlined text-primary-600">autorenew</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">{{ __('Renewal') }}</h4>
                        <p class="text-sm mt-1">{{ __('You can renew your lease at any time before expiration. We recommend renewing at least 7 days before expiry to ensure continuity.') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <span class="material-icons-outlined text-warning-600">receipt_long</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">{{ __('Invoices') }}</h4>
                        <p class="text-sm mt-1">{{ __('All invoices are available in your dashboard under "Invoices". You can download PDFs for your accounting records.') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-danger-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <span class="material-icons-outlined text-danger-600">cancel</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">{{ __('Termination') }}</h4>
                        <p class="text-sm mt-1">{{ __('You can request early termination of your lease, but refunds are subject to our terms of service. Please contact support for assistance.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-secondary-600 to-secondary-800 rounded-xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">{{ __('Ready to Get Started?') }}</h3>
            <p class="text-secondary-100 mb-6">{{ __('Browse our marketplace to find the perfect IP range for your business.') }}</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('marketplace.index') }}" class="bg-white text-secondary-600 px-8 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                    {{ __('Browse Marketplace') }}
                </a>
                <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white/10 transition-colors">
                    {{ __('Create Account') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
