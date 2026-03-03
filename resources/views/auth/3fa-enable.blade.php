@extends('layouts.auth')

@section('title', 'Xác Nhận Bật 2FA')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 px-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Xác Nhận Bật 2FA</h1>
                <p class="text-gray-600 text-sm mt-2">Nhập mã OTP được gửi đến email của bạn</p>
            </div>

            @if(session('message'))
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-6">
                    {{ session('message') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('3fa.verify-setup-otp') }}" method="POST">
                @csrf

                <!-- Email Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600">Mã OTP được gửi đến:</p>
                    <p class="font-semibold text-gray-800">{{ $email }}</p>
                </div>

                <!-- OTP Input -->
                <div class="mb-6">
                    <label for="otp" class="block text-gray-700 text-sm font-medium mb-2">Nhập Mã OTP</label>
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
                    Xác Thực & Bật 2FA
                </button>
            </form>

            <!-- Info -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-yellow-800 text-sm">Sau khi bật 2FA, bạn sẽ nhận các mã khôi phục. Lưu chúng ở nơi an toàn!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        
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
