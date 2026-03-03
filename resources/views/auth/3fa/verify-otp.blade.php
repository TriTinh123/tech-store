@extends('layouts.app')

@section('content')
@php
    /** @var \App\Models\User $user */
@endphp
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Header -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
                <h1 class="text-2xl font-bold text-white mb-2">
                    Xác thực 3FA
                </h1>
                <p class="text-blue-100">Vui lòng nhập mã OTP để hoàn tất đăng nhập</p>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <!-- User Info -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600">
                        <span class="font-semibold">Tài khoản:</span> {{ $user->email }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-semibold">Mã OTP được gửi đến:</span> {{ substr($user->email, 0, 3) }}***{{ substr($user->email, strrpos($user->email, '@')) }}
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                        <p class="text-sm font-semibold text-red-700 mb-2">Lỗi xác thực</p>
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- OTP Form -->
                <form method="POST" action="{{ route('3fa.verify-otp') }}" class="space-y-4">
                    @csrf

                    <!-- OTP Input -->
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                            Nhập mã OTP
                        </label>
                        <input
                            type="text"
                            id="otp"
                            name="otp"
                            placeholder="000000"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors {{ $errors->has('otp') ? 'border-red-500' : '' }}"
                            required
                            autofocus
                        />
                        <p class="text-sm text-gray-500 mt-2">
                            Mã OTP gồm 6 chữ số được gửi qua email
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Xác thực
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="px-3 text-sm text-gray-500">hoặc</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- Resend OTP -->
                <div class="space-y-3">
                    <p class="text-sm text-gray-600 text-center">
                        Không nhận được mã OTP?
                    </p>

                    @if(app()->isLocal())
                        <a
                            href="{{ route('debug.otp', ['userId' => $user->id]) }}"
                            class="block w-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold py-2 px-4 rounded-lg transition-colors text-center focus:outline-none focus:ring-2 focus:ring-amber-400"
                        >
                            👀 Xem mã OTP (Dev Mode)
                        </a>
                    @endif

                    <button
                        type="button"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400"
                        onclick="resendOtp()"
                    >
                        Gửi lại mã OTP
                    </button>
                </div>

                <!-- Footer Links -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        ← Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 p-4 bg-white rounded-lg shadow text-center">
            <p class="text-xs text-gray-600">
                🔒 Mã OTP sẽ hết hạn sau 10 phút. Để bảo mật, không chia sẻ mã này với ai khác.
            </p>
        </div>
    </div>
</div>

<script>
    function resendOtp() {
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Đang gửi...';

        fetch('{{ route('3fa.resend-otp') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                btn.textContent = 'Gửi lại mã OTP';
                btn.disabled = false;
            } else {
                alert(data.message);
                btn.textContent = 'Gửi lại mã OTP';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi khi gửi lại mã OTP');
            btn.textContent = 'Gửi lại mã OTP';
            btn.disabled = false;
        });
    }

    // Auto-focus next input when typing
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection
