@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Setup Two-Factor Authentication') }}</h2>

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Step 1: Scan QR Code') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Use your authenticator app (Google Authenticator, Authy, etc.) to scan this QR code.') }}
                    </p>

                    <div class="bg-white p-4 border rounded-lg inline-block mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
                            alt="QR Code" class="w-48 h-48">
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">{{ __("Can't scan the QR code? Enter this key manually:") }}</p>
                        <div class="bg-gray-100 p-3 rounded font-mono text-sm break-all select-all">
                            {{ $secret }}
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Step 2: Verify Code') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Enter the 6-digit code from your authenticator app to confirm setup.') }}
                    </p>

                    <form action="{{ route('two-factor.confirm') }}" method="POST">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <input type="text" name="code" maxlength="6" pattern="[0-9]{6}"
                                placeholder="000000"
                                class="w-32 text-center text-2xl tracking-widest rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                                required autofocus>
                            <button type="submit"
                                class="px-6 py-3 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition">
                                {{ __('Verify & Enable') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="border-t pt-6">
                    <a href="{{ route('two-factor.index') }}" class="text-gray-600 hover:text-gray-800">
                        &larr; {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
