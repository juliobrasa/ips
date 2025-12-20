<x-app-layout>
    <x-slot name="header">{{ __('New Ticket') }}</x-slot>
    <x-slot name="title">{{ __('Create Support Ticket') }}</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tickets.index') }}" class="hover:text-primary-600">{{ __('Tickets') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ __('New Ticket') }}</span>
        </div>

        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ __('Create Support Ticket') }}</h2>

            <form action="{{ route('tickets.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Subject') }} *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                           placeholder="{{ __('Brief description of your issue') }}">
                    @error('subject')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }} *</label>
                        <select name="category" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">{{ __('Select category') }}</option>
                            @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Priority') }} *</label>
                        <select name="priority" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @foreach($priorities as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Message') }} *</label>
                    <textarea name="message" rows="8" required
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                              placeholder="{{ __('Please describe your issue in detail...') }}">{{ old('message') }}</textarea>
                    @error('message')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">{{ __('Maximum 5000 characters') }}</p>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                        <span class="material-icons-outlined mr-2">send</span>
                        {{ __('Submit Ticket') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Box -->
        <div class="bg-info-50 rounded-xl p-6">
            <h3 class="font-semibold text-info-800 mb-3">{{ __('Tips for faster resolution') }}</h3>
            <ul class="space-y-2 text-sm text-info-700">
                <li class="flex items-start">
                    <span class="material-icons-outlined text-sm mr-2 mt-0.5">check_circle</span>
                    {{ __('Be specific about the issue you are experiencing') }}
                </li>
                <li class="flex items-start">
                    <span class="material-icons-outlined text-sm mr-2 mt-0.5">check_circle</span>
                    {{ __('Include relevant details like IP addresses, subnet CIDRs, or lease IDs') }}
                </li>
                <li class="flex items-start">
                    <span class="material-icons-outlined text-sm mr-2 mt-0.5">check_circle</span>
                    {{ __('Describe what you expected vs what actually happened') }}
                </li>
                <li class="flex items-start">
                    <span class="material-icons-outlined text-sm mr-2 mt-0.5">check_circle</span>
                    {{ __('Include any error messages you received') }}
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
