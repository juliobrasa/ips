<x-guest-layout>
    <x-slot name="title">{{ __('How to Add an IP Range for Lease') }}</x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <a href="{{ route('help.index') }}" class="inline-flex items-center text-primary-100 hover:text-white mb-4">
                <span class="material-icons-outlined mr-1">arrow_back</span>
                {{ __('Back to Help Center') }}
            </a>
            <h1 class="text-3xl font-bold">{{ __('How to Add an IP Range for Lease') }}</h1>
            <p class="text-primary-100 mt-2">{{ __('Complete guide to listing your IP addresses on our marketplace') }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Introduction -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ __('Overview') }}</h2>
            <p class="text-gray-600 mb-4">
                {{ __('As an IP Holder, you can list your unused IPv4 address ranges on our marketplace and earn passive income by leasing them to businesses that need additional IP addresses.') }}
            </p>
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                <div class="flex items-start">
                    <span class="material-icons-outlined text-primary-600 mr-3">info</span>
                    <div>
                        <p class="text-primary-800 font-medium">{{ __('Requirements') }}</p>
                        <ul class="text-primary-700 text-sm mt-2 space-y-1">
                            <li>{{ __('You must be the legitimate owner or authorized manager of the IP addresses') }}</li>
                            <li>{{ __('Your company must complete KYC verification') }}</li>
                            <li>{{ __('IP ranges must be registered with a Regional Internet Registry (RIR)') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">1</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Create an Account and Complete KYC') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('First, you need to register an account and complete the company verification process:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Click "Register" on the homepage and create your account') }}</li>
                    <li>{{ __('Verify your email address') }}</li>
                    <li>{{ __('Fill in your company information:') }}
                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1 text-sm">
                            <li>{{ __('Company legal name') }}</li>
                            <li>{{ __('Tax ID / VAT number') }}</li>
                            <li>{{ __('Business address') }}</li>
                            <li>{{ __('Select "IP Holder" as your account type') }}</li>
                        </ul>
                    </li>
                    <li>{{ __('Submit your KYC documents for verification') }}</li>
                    <li>{{ __('Wait for approval (typically 1-2 business days)') }}</li>
                </ol>

                <div class="bg-warning-50 border border-warning-200 rounded-lg p-4 mt-4">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-warning-600 mr-3">warning</span>
                        <p class="text-warning-800 text-sm">
                            {{ __('You cannot list IP ranges until your company is verified. This ensures all IP holders on our platform are legitimate.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">2</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Navigate to Subnets Management') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Once your company is verified:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('Log in to your dashboard') }}</li>
                    <li>{{ __('Click on "My Subnets" in the navigation menu') }}</li>
                    <li>{{ __('Click the "Add Subnet" button') }}</li>
                </ol>

                <div class="bg-gray-100 rounded-lg p-4 mt-4">
                    <p class="text-sm text-gray-500 mb-2">{{ __('Navigation path:') }}</p>
                    <div class="flex items-center text-gray-700">
                        <span class="bg-white px-3 py-1 rounded shadow-sm">{{ __('Dashboard') }}</span>
                        <span class="material-icons-outlined mx-2 text-gray-400">chevron_right</span>
                        <span class="bg-white px-3 py-1 rounded shadow-sm">{{ __('My Subnets') }}</span>
                        <span class="material-icons-outlined mx-2 text-gray-400">chevron_right</span>
                        <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded shadow-sm">{{ __('Add Subnet') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">3</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Fill in Subnet Information') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Complete the subnet registration form with the following information:') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <span class="material-icons-outlined text-primary-600 mr-2 text-lg">language</span>
                            {{ __('IP Address') }}
                        </h4>
                        <p class="text-sm">{{ __('Enter the network address of your subnet (e.g., 192.168.1.0)') }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <span class="material-icons-outlined text-primary-600 mr-2 text-lg">straighten</span>
                            {{ __('CIDR Prefix') }}
                        </h4>
                        <p class="text-sm">{{ __('Select the size of your subnet (/24, /23, /22, etc.)') }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <span class="material-icons-outlined text-primary-600 mr-2 text-lg">public</span>
                            {{ __('RIR') }}
                        </h4>
                        <p class="text-sm">{{ __('Select the Regional Internet Registry where your IPs are registered (RIPE, ARIN, LACNIC, APNIC, AFRINIC)') }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <span class="material-icons-outlined text-primary-600 mr-2 text-lg">location_on</span>
                            {{ __('Geolocation') }}
                        </h4>
                        <p class="text-sm">{{ __('Specify the country and city where the IPs are geolocated') }}</p>
                    </div>
                </div>

                <!-- CIDR Reference Table -->
                <div class="mt-6">
                    <h4 class="font-medium text-gray-800 mb-3">{{ __('CIDR Size Reference') }}</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">{{ __('CIDR') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Number of IPs') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Typical Use') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="px-4 py-2">/24</td>
                                    <td class="px-4 py-2">256</td>
                                    <td class="px-4 py-2">{{ __('Small business, single server rack') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/23</td>
                                    <td class="px-4 py-2">512</td>
                                    <td class="px-4 py-2">{{ __('Medium business, small data center') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/22</td>
                                    <td class="px-4 py-2">1,024</td>
                                    <td class="px-4 py-2">{{ __('Large business, data center') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/21</td>
                                    <td class="px-4 py-2">2,048</td>
                                    <td class="px-4 py-2">{{ __('Enterprise, hosting provider') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/20</td>
                                    <td class="px-4 py-2">4,096</td>
                                    <td class="px-4 py-2">{{ __('Large hosting provider, ISP') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">4</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Set Your Pricing') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Configure the pricing for your IP addresses:') }}</p>

                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">{{ __('Price per IP (Monthly)') }}</h4>
                            <p class="text-sm mb-3">{{ __('Set a competitive monthly price per IP address. Market rates typically range from $0.40 to $1.00 per IP.') }}</p>
                            <div class="bg-white rounded-lg border border-gray-200 p-3">
                                <p class="text-xs text-gray-500 mb-1">{{ __('Example for /24 (256 IPs):') }}</p>
                                <p class="text-lg font-semibold text-gray-800">$0.50 x 256 = <span class="text-success-600">$128/{{ __('month') }}</span></p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">{{ __('Minimum Lease Term') }}</h4>
                            <p class="text-sm mb-3">{{ __('Optionally set a minimum lease duration. Longer terms provide more stable income.') }}</p>
                            <ul class="text-sm space-y-1">
                                <li class="flex items-center">
                                    <span class="material-icons-outlined text-success-500 mr-2 text-sm">check_circle</span>
                                    {{ __('1 month (most flexible)') }}
                                </li>
                                <li class="flex items-center">
                                    <span class="material-icons-outlined text-success-500 mr-2 text-sm">check_circle</span>
                                    {{ __('3 months (recommended)') }}
                                </li>
                                <li class="flex items-center">
                                    <span class="material-icons-outlined text-success-500 mr-2 text-sm">check_circle</span>
                                    {{ __('6 or 12 months (stable income)') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-primary-600 mr-3">lightbulb</span>
                        <div>
                            <p class="text-primary-800 font-medium">{{ __('Pricing Tips') }}</p>
                            <ul class="text-primary-700 text-sm mt-2 space-y-1">
                                <li>{{ __('Clean IPs with good reputation can command higher prices') }}</li>
                                <li>{{ __('IPs in certain geolocations (US, EU) are typically more valuable') }}</li>
                                <li>{{ __('Offering discounts for longer terms can attract more lessees') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold mr-4">5</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Submit for Verification') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('After submitting your subnet, our team will verify your ownership:') }}</p>

                <ol class="list-decimal list-inside space-y-3 ml-4">
                    <li>{{ __('We will check the WHOIS records to confirm your organization owns the IP range') }}</li>
                    <li>{{ __('We verify the IP range is not already listed elsewhere') }}</li>
                    <li>{{ __('We run an initial reputation check on the IP addresses') }}</li>
                    <li>{{ __('You may be asked to create a specific RIPE/ARIN object or respond from the abuse contact email') }}</li>
                </ol>

                <div class="bg-gray-100 rounded-lg p-4 mt-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-warning-600 mr-2">schedule</span>
                            <span class="text-gray-700">{{ __('Verification Time:') }}</span>
                        </div>
                        <span class="font-medium text-gray-800">{{ __('24-48 business hours') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="bg-white rounded-xl shadow-material-1 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-success-600 text-white rounded-full flex items-center justify-center font-bold mr-4">6</div>
                <h2 class="text-xl font-bold text-gray-800">{{ __('Go Live and Start Earning') }}</h2>
            </div>

            <div class="space-y-4 text-gray-600">
                <p>{{ __('Once verified, your subnet will be listed on the marketplace:') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-success-50 rounded-lg p-4 text-center">
                        <span class="material-icons-outlined text-success-600 text-3xl mb-2">visibility</span>
                        <h4 class="font-medium text-gray-800">{{ __('Listed') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Your subnet appears in the marketplace') }}</p>
                    </div>

                    <div class="bg-success-50 rounded-lg p-4 text-center">
                        <span class="material-icons-outlined text-success-600 text-3xl mb-2">shopping_cart</span>
                        <h4 class="font-medium text-gray-800">{{ __('Leased') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Businesses can lease your IPs') }}</p>
                    </div>

                    <div class="bg-success-50 rounded-lg p-4 text-center">
                        <span class="material-icons-outlined text-success-600 text-3xl mb-2">payments</span>
                        <h4 class="font-medium text-gray-800">{{ __('Paid') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Receive payouts twice monthly') }}</p>
                    </div>
                </div>

                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4 mt-6">
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-primary-600 mr-3">account_balance</span>
                        <div>
                            <p class="text-primary-800 font-medium">{{ __('Payout Schedule') }}</p>
                            <p class="text-primary-700 text-sm mt-1">
                                {{ __('Payouts are processed on the 1st and 15th of each month. A 10% platform fee is deducted from your earnings. Minimum payout threshold is $100.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">{{ __('Ready to Start Earning?') }}</h3>
            <p class="text-primary-100 mb-6">{{ __('List your IP addresses today and start generating passive income.') }}</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-white text-primary-600 px-8 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                    {{ __('Create Account') }}
                </a>
                <a href="{{ route('marketplace.index') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white/10 transition-colors">
                    {{ __('View Marketplace') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
