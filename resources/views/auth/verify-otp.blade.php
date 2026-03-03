@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Identity</h2>
            <p class="text-gray-600">Enter the 6-digit code sent to your email</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- OTP Form -->
        <form method="POST" action="{{ route('3fa.verify-otp') }}" class="space-y-4">
            @csrf

            <!-- OTP Input -->
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                    Verification Code
                </label>
                <input
                    type="text"
                    name="otp"
                    id="otp"
                    inputmode="numeric"
                    pattern="\d{6}"
                    maxlength="6"
                    placeholder="000000"
                    value="{{ old('otp') }}"
                    class="w-full px-4 py-3 text-center text-2xl tracking-widest border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('otp') border-red-500 @enderror"
                    autofocus
                    required
                >
                <p class="text-sm text-gray-500 mt-1">Enter 6 digits only</p>
            </div>

            <!-- Expiration Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                <p class="font-medium mb-1">⏱️ Code expires at:</p>
                <p>{{ $expiresAt->format('H:i:s') }}</p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
            >
                Verify Code
            </button>

            <!-- Resend Link -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                <form method="POST" action="{{ route('3fa.resend-otp') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                    >
                        Resend OTP
                    </button>
                </form>
            </div>
        </form>

        <!-- Cancel Link -->
        <div class="text-center mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('3fa.cancel') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                Cancel verification
            </a>
        </div>
    </div>
</div>
@endsection
