@extends('layouts.auth')

@section('title', 'Mã Khôi Phục 2FA')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 px-4">
    <div class="w-full max-w-lg">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">2FA Đã Bật Thành Công!</h1>
                <p class="text-gray-600 text-sm mt-2">Hãy lưu các mã khôi phục của bạn</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Warning -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-800 text-sm mb-1">Quan Trọng!</p>
                        <p class="text-red-700 text-sm">Hãy lưu các mã này ở nơi an toàn (ví dụ: 1password, LastPass, hoặc một tệp được mã hóa). Nếu bạn mất quyền truy cập vào thiết bị 2FA, bạn sẽ cần các mã này để truy cập tài khoản của mình.</p>
                    </div>
                </div>
            </div>

            <!-- Recovery Codes -->
            <div class="mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Mã Khôi Phục của Bạn</h2>
                <div class="grid grid-cols-2 gap-3 bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                    @foreach($codes as $code)
                        <div class="font-mono text-sm font-semibold text-gray-800 bg-white p-3 rounded border border-gray-200 text-center">
                            {{ $code }}
                        </div>
                    @endforeach
                </div>
                <p class="text-gray-600 text-xs mt-3 text-center">Mỗi mã chỉ có thể sử dụng một lần</p>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <button 
                    onclick="downloadRecoveryCodes()"
                    class="flex items-center justify-center gap-2 px-4 py-3 border border-blue-300 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Tải Xuống
                </button>
                
                <button 
                    onclick="printRecoveryCodes()"
                    class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H7a2 2 0 01-2-2v-4a2 2 0 012-2h10a2 2 0 012 2v4a2 2 0 01-2 2zm2-8a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    In
                </button>
            </div>

            <!-- Continue Button -->
            <a 
                href="{{ route('security.setup') }}"
                class="w-full block text-center bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200"
            >
                Hoàn Tất Cấu Hình
            </a>
        </div>

        <!-- Info -->
        <div class="text-white text-center mt-8">
            <p class="text-sm">
                Tài khoản của bạn giờ được bảo vệ bằng xác thực hai yếu tố.<br>
                <a href="{{ route('security.setup') }}" class="underline hover:opacity-80">Quay lại cấu hình bảo mật</a>
            </p>
        </div>
    </div>
</div>

<script>
    function downloadRecoveryCodes() {
        const codes = @json($codes);
        const content = "MÃ KHÔI PHỤC 2FA - TechStore\n" +
                       "Ngày tạo: " + new Date().toLocaleString() + "\n" +
                       "=====================================\n" +
                       codes.join("\n") +
                       "\n\nGhi chú: Hãy lưu mã này ở nơi an toàn. Mỗi mã chỉ sử dụng được một lần.";
        
        const blob = new Blob([content], {type: 'text/plain'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'recovery-codes.txt';
        a.click();
        URL.revokeObjectURL(url);
    }

    function printRecoveryCodes() {
        window.print();
    }
</script>

<style>
    @media print {
        body {
            background: white;
        }
        .w-full.max-w-lg > div:not(:first-child),
        button, a[href] {
            display: none;
        }
    }
</style>
@endsection
