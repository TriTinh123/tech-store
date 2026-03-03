@extends('layouts.app')

@section('title', 'Cài đặt bảo mật')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Cài đặt bảo mật</h1>
            <p class="mt-2 text-gray-600">Quản lý cấu hình bảo mật toàn hệ thống</p>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Configs -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cấu hình bảo mật</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_configs'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Configs -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cấu hình hoạt động</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_configs'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Blocked IPs -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M9 3h6a9 9 0 019 9v6a9 9 0 01-9 9H9a9 9 0 01-9-9v-6a9 9 0 019-9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">IP bị chặn</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['blocked_ips'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Responses -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Phản ứng chờ xử lý</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_responses'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Hành động nhanh</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.security-settings.edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Chỉnh sửa cài đặt
                </a>
                <a href="{{ route('admin.security-settings.blocked-ips') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quản lý IP chặn
                </a>
                <a href="{{ route('admin.security-settings.auto-responses') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Phản ứng tự động
                </a>
                <a href="{{ route('admin.security-settings.statistics') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Thống kê
                </a>
            </div>
        </div>

        <!-- Current Configuration Display -->
        @if ($currentConfig)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Cấu hình hiện tại</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Auth Level -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Mức độ xác thực</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $currentConfig->getAuthLevelLabel() }}</p>
                </div>

                <!-- OTP Method -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Phương pháp OTP</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $currentConfig->getOtpMethodLabel() }}</p>
                </div>

                <!-- Security Questions -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Yêu cầu câu hỏi bảo mật</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($currentConfig->require_security_questions) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                            @if($currentConfig->require_security_questions) Bật @else Tắt @endif
                        </span>
                    </p>
                </div>

                <!-- IP Blocking -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Chặn IP tự động</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($currentConfig->enable_ip_blocking) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                            @if($currentConfig->enable_ip_blocking) Bật @else Tắt @endif
                        </span>
                    </p>
                </div>

                <!-- Anomaly Detection -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Phát hiện bất thường</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($currentConfig->anomaly_detection_enabled) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                            @if($currentConfig->anomaly_detection_enabled) Bật @else Tắt @endif
                        </span>
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Trạng thái</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($currentConfig->enable_security_config) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                            @if($currentConfig->enable_security_config) Hoạt động @else Vô hiệu @endif
                        </span>
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.security-settings.edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Sửa cài đặt này
                </a>
            </div>
        </div>
        @endif

        <!-- Blocking Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- IP Blocking Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thống kê chặn IP</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Tổng IP bị chặn</span>
                        <span class="font-semibold text-gray-900">{{ $stats['blocking_stats']['total_blocked'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Chặn vĩnh viễn</span>
                        <span class="font-semibold text-gray-900">{{ $stats['blocking_stats']['permanent_blocks'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Chặn tạm thời</span>
                        <span class="font-semibold text-gray-900">{{ $stats['blocking_stats']['temporary_blocks'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Tự động chặn</span>
                        <span class="font-semibold text-gray-900">{{ $stats['blocking_stats']['auto_blocked'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Chặn thủ công</span>
                        <span class="font-semibold text-gray-900">{{ $stats['blocking_stats']['manual_blocked'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Response Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thống kê phản ứng tự động</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Tổng phản ứng</span>
                        <span class="font-semibold text-gray-900">{{ $stats['response_stats']['total_responses'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Chờ xử lý</span>
                        <span class="font-semibold text-yellow-600">{{ $stats['response_stats']['pending'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Đã thực thi</span>
                        <span class="font-semibold text-green-600">{{ $stats['response_stats']['executed'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Thất bại</span>
                        <span class="font-semibold text-red-600">{{ $stats['response_stats']['failed'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Tỷ lệ thành công</span>
                        <span class="font-semibold text-gray-900">{{ $stats['response_stats']['success_rate'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
