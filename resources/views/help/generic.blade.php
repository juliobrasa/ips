<x-guest-layout>
    <x-slot name="title">{{ $guide['meta_title'] }}</x-slot>
    <x-slot name="metaDescription">{{ $guide['meta_description'] }}</x-slot>

    <!-- Breadcrumb -->
    <div class="bg-gradient-to-r from-primary-600 to-secondary-600 text-white py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm mb-4">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:underline opacity-80">Home</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li><a href="{{ route('help.index') }}" class="hover:underline opacity-80">Help Center</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li class="opacity-80">{{ $guide['title'] }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold">{{ $guide['title'] }}</h1>
            <p class="mt-2 text-lg opacity-90">{{ $guide['description'] }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-material-1 p-8">
                    <div class="text-center py-12">
                        <span class="material-icons-outlined text-6xl text-gray-300 mb-4">construction</span>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Coming Soon</h2>
                        <p class="text-gray-600 max-w-md mx-auto">
                            We're currently working on this guide. Check back soon for comprehensive information on {{ strtolower($guide['title']) }}.
                        </p>

                        <div class="mt-8">
                            <a href="{{ route('help.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                <span class="material-icons-outlined mr-2">arrow_back</span>
                                Back to Help Center
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Info Preview -->
                <div class="bg-gray-50 rounded-xl p-6 mt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">What This Guide Will Cover</h3>
                    <p class="text-gray-600">{{ $guide['description'] }}</p>

                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <span class="material-icons-outlined text-sm mr-1">schedule</span>
                        Expected soon
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Contact Support -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Need Help Now?</h3>
                    <p class="text-sm text-gray-600 mb-4">Our support team is available to answer your questions while we complete this guide.</p>
                    <a href="mailto:support@soltia.io" class="inline-flex items-center text-primary-600 hover:text-primary-800">
                        <span class="material-icons-outlined mr-2 text-sm">email</span>
                        Contact Support
                    </a>
                </div>

                <!-- Related Guides -->
                @if(count($relatedGuides) > 0)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Related Guides</h3>
                    <ul class="space-y-3">
                        @foreach($relatedGuides as $related)
                        <li>
                            <a href="{{ route('help.show', $related['slug']) }}" class="text-gray-700 hover:text-primary-600 flex items-start">
                                <span class="material-icons-outlined text-sm mr-2 mt-0.5 text-gray-400">article</span>
                                {{ $related['title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
