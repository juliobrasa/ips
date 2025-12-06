<x-guest-layout>
    <x-slot name="title">{{ __('Login') }} - {{ __('Soltia IPS Marketplace') }}</x-slot>

    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">{{ __('Welcome Back') }}</h2>
        <p class="text-gray-500 text-center mb-8">{{ __('Sign in to your account') }}</p>

        <!-- Session Status -->
        @if (session('status'))
        <div class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-lg text-sm">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email Address') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <span class="material-icons-outlined text-xl">mail</span>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
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
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                           placeholder="{{ __('Password') }}">
                </div>
                @error('password')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-700">
                    {{ __('Forgot password?') }}
                </a>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center justify-center">
                <span class="material-icons-outlined mr-2">login</span>
                {{ __('Sign In') }}
            </button>
        </form>
    </div>

    <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                {{ __('Create one') }}
            </a>
        </p>
    </div>
</x-guest-layout>
