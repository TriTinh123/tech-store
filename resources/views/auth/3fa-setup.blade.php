@extends('layouts.app')

@section('title', 'Cấu Hình Xác Thực Hai Yếu Tố')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Xác Thực Hai Yếu Tố (2FA)</h1>
                <p class="text-gray-600 mt-2">Bảo vệ tài khoản của bạn bằng xác thực hai yếu tố</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Status Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Trạng Thái Hiện Tại</h2>
        @php
            $otpMethod = $methods->firstWhere('method_type', 'otp_email');
        @endphp
        
        @if($otpMethod)
            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-green-700 font-medium">2FA Đã Được Bật</span>
                </div>
                <form action="{{ route('3fa.disable') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="method" value="otp_email">
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required class="px-3 py-2 border border-gray-300 rounded mr-2 text-sm">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">Tắt 2FA</button>
                </form>
            </div>
        @else
            <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 010 10c0-3.313 2.686-6 6-6 1.657 0 3.172.5 4.472 1.35m2.792 13.206A6 6 0 0120 10c0-3.313-2.686-6-6-6-1.657 0-3.172.5-4.472 1.35m0 0a6 6 0 00-9.464 9.464m19.414-2.8l-1.414 1.414M5.586 5.586L4.172 4.172m0 0a6 6 0 009.464 9.464" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-700 font-medium">2FA Chưa Được Bật</span>
                </div>
                <form action="{{ route('3fa.enable-otp') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Bật 2FA</button>
                </form>
            </div>
        @endif
    </div>

    <!-- Methods List -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Phương Pháp Xác Thực</h2>
        
        <!-- OTP Email -->
        <div class="border rounded-lg p-4 mb-4">
            <div class="flex items-start justify-between">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1 flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Mã OTP qua Email</h3>
                        <p class="text-gray-600 text-sm mt-1">Nhận mã xác thực 6 chữ số qua email đăng ký</p>
                        @if($otpMethod)
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded mt-2">✓ Đã Bật</span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded mt-2">Chưa Bật</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Questions (Placeholder) -->
        <div class="border rounded-lg p-4 mb-4 opacity-50">
            <div class="flex items-start justify-between">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3 mt-1 flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Câu Hỏi Bảo Mật</h3>
                        <p class="text-gray-600 text-sm mt-1">Sắp có - Trả lời các câu hỏi bảo mật</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recovery Codes Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Mã Khôi Phục</h2>
        
        @php
            $recoveryCodes = $methods->firstWhere('method_type', 'recovery_codes');
        @endphp
        
        @if($recoveryCodes)
            <div class="mb-4">
                <p class="text-gray-600 text-sm mb-3">Lưu các mã này ở nơi an toàn. Bạn có thể sử dụng chúng để truy cập tài khoản nếu không có quyền truy cập vào thiết bị xác thực của bạn.</p>
                
                @php
                    $codes = json_decode($recoveryCodes->security_questions, true);
                @endphp
                
                <div class="grid grid-cols-2 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach($codes as $code)
                        <div class="font-mono text-sm text-gray-800 bg-white p-2 rounded border border-gray-200">{{ $code }}</div>
                    @endforeach
                </div>

                <div class="mt-4 flex gap-3">
                    <button 
                        onclick="printRecoveryCodes()"
                        class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H7a2 2 0 01-2-2v-4a2 2 0 012-2h10a2 2 0 012 2v4a2 2 0 01-2 2zm2-8a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        In
                    </button>
                    
                    <form action="{{ route('3fa.regenerate-codes') }}" method="POST" class="inline">
                        @csrf
                        <input type="password" name="password" placeholder="Nhập mật khẩu" required class="px-3 py-2 border border-gray-300 rounded text-sm mr-2">
                        <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 rounded hover:bg-red-50">Tạo Lại Mã</button>
                    </form>
                </div>
            </div>
        @else
            <p class="text-gray-600">Mã khôi phục sẽ được tạo khi bạn bật 2FA.</p>
        @endif
    </div>
</div>

<script>
    function printRecoveryCodes() {
        window.print();
    }
</script>

@include('components.styles.security')
@endsection
