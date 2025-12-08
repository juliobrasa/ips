@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="text-center mb-6">
            <svg class="w-16 h-16 mx-auto text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">{{ __('Two-Factor Authentication') }}</h2>
            <p class="mt-2 text-sm text-gray-600">{{ __('Please enter the verification code from your authenticator app.') }}</p>
        </div>

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                {{ session('info') }}
            </div>
        @endif

        <div x-data="{ showRecovery: false }">
            <!-- Verification Code Form -->
            <form x-show="!showRecovery" action="{{ route('two-factor.verify') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Verification Code') }}
                    </label>
                    <input type="text" name="code" id="code" maxlength="6" pattern="[0-9]{6}"
                        class="w-full text-center text-3xl tracking-[0.5em] py-3 rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        placeholder="000000" autofocus autocomplete="one-time-code">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition">
                    {{ __('Verify') }}
                </button>
            </form>

            <!-- Recovery Code Form -->
            <form x-show="showRecovery" x-cloak action="{{ route('two-factor.verify') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Recovery Code') }}
                    </label>
                    <input type="text" name="recovery_code" id="recovery_code"
                        class="w-full text-center font-mono py-3 rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        placeholder="XXXX-XXXX" autocomplete="off">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition">
                    {{ __('Use Recovery Code') }}
                </button>
            </form>

            <div class="mt-4 text-center">
                <button @click="showRecovery = !showRecovery" type="button"
                    class="text-sm text-rose-600 hover:text-rose-800">
                    <span x-show="!showRecovery">{{ __('Use a recovery code') }}</span>
                    <span x-show="showRecovery" x-cloak>{{ __('Use verification code') }}</span>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-800">
                &larr; {{ __('Back to login') }}
            </a>
        </div>
    </div>
</div>
@endsection
