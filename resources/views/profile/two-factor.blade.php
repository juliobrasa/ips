@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Two-Factor Authentication') }}</h2>

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($enabled)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="text-green-800 font-medium">{{ __('Two-factor authentication is enabled') }}</span>
                        </div>
                    </div>

                    <!-- Recovery Codes -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Recovery Codes') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Store these recovery codes in a secure location. They can be used to access your account if you lose your two-factor authentication device.') }}
                        </p>
                        <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm grid grid-cols-2 gap-2">
                            @foreach ($recoveryCodes as $code)
                                <div class="text-gray-800">{{ $code }}</div>
                            @endforeach
                        </div>

                        <form action="{{ route('two-factor.regenerate-codes') }}" method="POST" class="mt-4">
                            @csrf
                            <div class="flex items-center space-x-4">
                                <input type="password" name="password" placeholder="{{ __('Current password') }}"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                                    required>
                                <button type="submit"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                                    {{ __('Regenerate Codes') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Disable 2FA -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Disable Two-Factor Authentication') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('If you disable two-factor authentication, your account will be less secure.') }}
                        </p>
                        <form action="{{ route('two-factor.disable') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="flex items-center space-x-4">
                                <input type="password" name="password" placeholder="{{ __('Current password') }}"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                                    required>
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                    {{ __('Disable 2FA') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-yellow-800 font-medium">{{ __('Two-factor authentication is not enabled') }}</span>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6">
                        {{ __('Add an extra layer of security to your account by enabling two-factor authentication. When enabled, you will need to enter a verification code from your authenticator app in addition to your password.') }}
                    </p>

                    <a href="{{ route('two-factor.enable') }}"
                        class="inline-flex items-center px-6 py-3 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        {{ __('Enable Two-Factor Authentication') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
