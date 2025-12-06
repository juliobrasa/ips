<x-guest-layout>
    <x-slot name="title">{{ __('Register') }} - {{ __('Soltia IPS Marketplace') }}</x-slot>

    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">{{ __('Create Account') }}</h2>
        <p class="text-gray-500 text-center mb-8">{{ __('Join the IP address marketplace') }}</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Full Name') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <span class="material-icons-outlined text-xl">person</span>
                    </span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                           placeholder="John Doe">
                </div>
                @error('name')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email Address') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <span class="material-icons-outlined text-xl">mail</span>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                           placeholder="you@company.com">
                </div>
                @error('email')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <span class="material-icons-outlined text-xl">lock</span>
                    </span>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                           placeholder="{{ __('Password') }}">
                </div>
                @error('password')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Confirm Password') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <span class="material-icons-outlined text-xl">lock</span>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                           placeholder="{{ __('Confirm Password') }}">
                </div>
                @error('password_confirmation')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Terms -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" name="terms" required class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-600">
                        {{ __('I agree to the') }} <a href="#" class="text-primary-600 hover:text-primary-700">{{ __('Terms of Service') }}</a>
                        {{ __('and') }} <a href="#" class="text-primary-600 hover:text-primary-700">{{ __('Privacy Policy') }}</a>
                    </span>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center justify-center">
                <span class="material-icons-outlined mr-2">person_add</span>
                {{ __('Create Account') }}
            </button>
        </form>
    </div>

    <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                {{ __('Sign In') }}
            </a>
        </p>
    </div>
</x-guest-layout>
