{{-- @var \App\Models\User $user --}}
@extends('layouts.app')
@php
    /** @var \App\Models\User $user */
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-teal-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Header -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-8">
                <h1 class="text-2xl font-bold text-white mb-2">
                    Xác thực sinh trắc học
                </h1>
                <p class="text-green-100">Vui lòng quét vân tay hoặc khuôn mặt</p>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <!-- User Info -->
                <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm text-gray-600">
                        <span class="font-semibold">Tài khoản:</span> {{ $user->email }}
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

                <!-- Biometric Options -->
                @if (!empty($biometrics))
                    <div class="space-y-4 mb-6">
                        @foreach ($biometrics as $biometric)
                            <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer biometric-option" data-type="{{ $biometric['type'] }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">
                                            @if ($biometric['type'] === 'fingerprint')
                                                👆 Vân tay
                                            @elseif ($biometric['type'] === 'face_id')
                                                😊 Khuôn mặt
                                            @else
                                                👁️ Quét mống mắt
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="font-medium">Thiết bị:</span> {{ $biometric['device_name'] }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Độ chính xác:</span> {{ $biometric['success_rate'] }}%
                                        </p>
                                        @if ($biometric['last_verified'])
                                            <p class="text-sm text-gray-500 mt-1">
                                                Lần cuối xác thực: {{ $biometric['last_verified'] }}
                                            </p>
                                        @endif
                                    </div>
                                    <input
                                        type="radio"
                                        name="biometric_type"
                                        value="{{ $biometric['type'] }}"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
                        <p class="text-sm text-yellow-800">
                            ⚠️ Chưa cấu hình phương pháp xác thực sinh trắc học nào
                        </p>
                    </div>
                @endif

                <!-- Biometric Form -->
                <form method="POST" action="{{ route('3fa.verify-biometric') }}" id="biometricForm" class="space-y-4">
                    @csrf

                    <!-- Biometric Data (hidden) -->
                    <input type="hidden" name="biometric_data" id="biometricData"/>

                    <!-- Scan Button -->
                    <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                        <div class="text-5xl mb-4">{{ $biometrics[0]['type'] === 'fingerprint' ? '👆' : '😊' }}</div>
                        <p class="text-gray-600 font-medium mb-4">
                            Đặt ngón tay lên cảm biến hoặc hướng mặt về camera
                        </p>
                        <button
                            type="button"
                            id="scanBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                            Bắt đầu quét
                        </button>
                    </div>

                    <!-- Status Message -->
                    <div id="statusMsg" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700" id="statusText">Đang quét...</p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        id="submitBtn"
                        disabled
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        Xác thực
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        ← Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('scanBtn').addEventListener('click', function() {
        // Simulate biometric scanning
        // In real implementation, integrate with native biometric APIs
        // like WebAuthn, TouchID, FaceID, etc.

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Đang quét...';

        const statusMsg = document.getElementById('statusMsg');
        const statusText = document.getElementById('statusText');
        statusMsg.classList.remove('hidden');

        // Simulate scan delay (2-3 seconds)
        setTimeout(() => {
            // Generate mock biometric data
            const mockData = 'mock_biometric_' + Math.random().toString(36).substr(2, 9);
            document.getElementById('biometricData').value = mockData;

            statusText.textContent = '✓ Quét thành công!';
            statusMsg.classList.remove('hidden');
            statusMsg.className = 'p-4 bg-green-50 border border-green-200 rounded-lg';
            statusText.className = 'text-sm text-green-700';

            document.getElementById('submitBtn').disabled = false;

            btn.disabled = false;
            btn.textContent = 'Quét lại';
        }, 2000);
    });
</script>
@endsection
