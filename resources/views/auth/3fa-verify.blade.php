@extends('layouts.auth')

@section('title', 'Xác Thực Hai Yếu Tố')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 px-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Xác Thực Hai Yếu Tố</h1>
                <p class="text-gray-600 text-sm mt-2">Vui lòng nhập mã OTP được gửi đến email của bạn</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('3fa.verify.submit') }}" method="POST">
                @csrf

                <!-- OTP Input -->
                <div class="mb-6">
                    <label for="otp" class="block text-gray-700 text-sm font-medium mb-2">Mã OTP (6 chữ số)</label>
                    <input 
                        type="text" 
                        id="otp" 
                        name="otp" 
                        maxlength="6" 
                        pattern="[0-9]{6}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-2xl tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('otp') border-red-500 @enderror"
                        placeholder="000000"
                        required
                        autofocus
                    >
                    <p class="text-gray-600 text-xs mt-2">Mã hết hạn trong 10 phút</p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200"
                >
                    Xác Thực
                </button>

                <!-- Recovery Code Option -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">Không nhận được mã?</p>
                    <button 
                        type="button" 
                        onclick="showRecoveryCodeForm()"
                        class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-1"
                    >
                        Sử dụng mã khôi phục
                    </button>
                </div>
            </form>

            <!-- Recovery Code Form (Hidden) -->
            <form id="recoveryForm" action="{{ route('3fa.recovery-code.submit') }}" method="POST" class="hidden mt-6">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-gray-700 text-sm font-medium mb-2">Mã Khôi Phục</label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="XXXXXXXX"
                        required
                    >
                </div>
                <button 
                    type="submit" 
                    class="w-full bg-gray-600 text-white font-semibold py-3 rounded-lg hover:bg-gray-700"
                >
                    Xác Thực Bằng Mã Khôi Phục
                </button>
                <button 
                    type="button" 
                    onclick="showRecoveryCodeForm()"
                    class="w-full text-gray-600 text-sm mt-2 py-2"
                >
                    ← Quay lại nhập OTP
                </button>
            </form>
        </div>

        <!-- Help Text -->
        <div class="text-center mt-8">
            <p class="text-white text-sm">
                Sau khi xác thực thành công, tài khoản của bạn sẽ được bảo vệ bằng xác thực hai yếu tố.
            </p>
        </div>
    </div>
</div>

<script>
    function showRecoveryCodeForm() {
        document.getElementById('otpForm').classList.toggle('hidden');
        document.getElementById('recoveryForm').classList.toggle('hidden');
    }

    // Auto-focus next input for OTP
    document.getElementById('otp').addEventListener('input', function(e) {
        // Only allow digits
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        
        // Auto-submit if 6 digits entered
        if (this.value.length === 6) {
            this.form.submit();
        }
    });
</script>

<style>
    #otp {
        letter-spacing: 0.5em;
    }
</style>
@endsection
