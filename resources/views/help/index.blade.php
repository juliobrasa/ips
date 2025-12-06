<x-guest-layout>
    <x-slot name="title">{{ __('Help Center') }}</x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">{{ __('Help Center') }}</h1>
                <p class="text-xl text-primary-100 max-w-2xl mx-auto">
                    {{ __('Find guides and tutorials to help you get started with our IP marketplace.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Guides Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">{{ __('Getting Started Guides') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($guides as $guide)
            <a href="{{ route('help.show', $guide['slug']) }}"
               class="bg-white rounded-xl shadow-material-1 p-8 hover:shadow-material-2 transition-shadow group">
                <div class="flex items-start">
                    <div class="w-14 h-14 bg-{{ $guide['color'] }}-100 rounded-xl flex items-center justify-center mr-5 group-hover:bg-{{ $guide['color'] }}-200 transition-colors">
                        <span class="material-icons-outlined text-{{ $guide['color'] }}-600 text-2xl">{{ $guide['icon'] }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-{{ $guide['color'] }}-600 transition-colors">
                            {{ $guide['title'] }}
                        </h3>
                        <p class="text-gray-600">{{ $guide['description'] }}</p>
                        <div class="mt-4 flex items-center text-{{ $guide['color'] }}-600 font-medium">
                            {{ __('Read Guide') }}
                            <span class="material-icons-outlined ml-1 text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">{{ __('Frequently Asked Questions') }}</h2>

            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between text-left">
                        <span class="font-medium text-gray-800">{{ __('What is an IP address lease?') }}</span>
                        <span class="material-icons-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600">
                            {{ __('An IP address lease is a temporary agreement where you rent IPv4 addresses from a holder for a specified period. This is useful for businesses that need additional IP addresses without the commitment of purchasing them outright.') }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between text-left">
                        <span class="font-medium text-gray-800">{{ __('How is pricing calculated?') }}</span>
                        <span class="material-icons-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600">
                            {{ __('Pricing is set per IP address per month. For example, if a /24 subnet (256 IPs) is priced at $0.50 per IP, your monthly cost would be $128. Longer lease terms may qualify for discounts.') }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between text-left">
                        <span class="font-medium text-gray-800">{{ __('What is a Letter of Authorization (LOA)?') }}</span>
                        <span class="material-icons-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600">
                            {{ __('A Letter of Authorization is an official document that proves you have permission to use and announce the leased IP addresses. You will need this document to configure BGP routing with your upstream providers.') }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between text-left">
                        <span class="font-medium text-gray-800">{{ __('What is KYC verification?') }}</span>
                        <span class="material-icons-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600">
                            {{ __('KYC (Know Your Customer) verification is a process to verify the identity and legitimacy of companies using our platform. This helps prevent fraud and ensures compliance with regulations. Verification typically takes 1-2 business days.') }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between text-left">
                        <span class="font-medium text-gray-800">{{ __('What is IP reputation?') }}</span>
                        <span class="material-icons-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600">
                            {{ __('IP reputation indicates whether an IP address or range has been associated with spam, malware, or other malicious activities. Clean IPs with high reputation scores are more valuable and less likely to face deliverability issues or blocks.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-16 bg-gray-50 rounded-xl p-8 text-center">
            <span class="material-icons-outlined text-5xl text-gray-400 mb-4">support_agent</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('Need More Help?') }}</h3>
            <p class="text-gray-600 mb-6">{{ __("Can't find what you're looking for? Our support team is here to help.") }}</p>
            <a href="mailto:support@soltia.net" class="inline-flex items-center bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <span class="material-icons-outlined mr-2">email</span>
                {{ __('Contact Support') }}
            </a>
        </div>
    </div>
</x-guest-layout>
