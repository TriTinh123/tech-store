@extends('layouts.app')

@section('title', 'Chỉnh sửa cài đặt bảo mật')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa cài đặt bảo mật</h1>
            <p class="mt-2 text-gray-600">Cấu hình các tính năng bảo mật cho toàn hệ thống</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Có lỗi trong form:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.security-settings.update') }}" method="POST" class="bg-white rounded-lg shadow-lg p-8">
            @csrf
            @method('PUT')

            <!-- Authentication Settings -->
            <fieldset class="mb-8 pb-8 border-b">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Cấu hình xác thực
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Auth Level -->
                    <div>
                        <label for="auth_level" class="block text-sm font-medium text-gray-700">
                            Mức độ xác thực <span class="text-red-500">*</span>
                        </label>
                        <select id="auth_level" name="auth_level" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="basic" @selected($config->auth_level === 'basic')>Cơ bản (Basic)</option>
                            <option value="standard" @selected($config->auth_level === 'standard')>Tiêu chuẩn (Standard)</option>
                            <option value="strict" @selected($config->auth_level === 'strict')>Nghiêm ngặt (Strict)</option>
                            <option value="ultra" @selected($config->auth_level === 'ultra')>Cực cao (Ultra)</option>
                        </select>
                    </div>

                    <!-- Require OTP -->
                    <div class="flex items-center">
                        <input type="hidden" name="require_otp" value="0">
                        <input type="checkbox" id="require_otp" name="require_otp" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->require_otp)>
                        <label for="require_otp" class="ml-2 block text-sm text-gray-700">Yêu cầu xác thực OTP</label>
                    </div>

                    <!-- OTP Method -->
                    <div>
                        <label for="otp_method" class="block text-sm font-medium text-gray-700">
                            Phương pháp OTP <span class="text-red-500">*</span>
                        </label>
                        <select id="otp_method" name="otp_method" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="email" @selected($config->otp_method === 'email')>Email</option>
                            <option value="sms" @selected($config->otp_method === 'sms')>SMS</option>
                            <option value="both" @selected($config->otp_method === 'both')>Email & SMS</option>
                        </select>
                    </div>

                    <!-- OTP Expiry -->
                    <div>
                        <label for="otp_expiry_minutes" class="block text-sm font-medium text-gray-700">
                            Thời gian hết hạn OTP (phút) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="otp_expiry_minutes" name="otp_expiry_minutes" min="5" max="60" value="{{ $config->otp_expiry_minutes ?? 10 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Security Questions -->
                    <div class="flex items-center">
                        <input type="hidden" name="require_security_questions" value="0">
                        <input type="checkbox" id="require_security_questions" name="require_security_questions" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->require_security_questions)>
                        <label for="require_security_questions" class="ml-2 block text-sm text-gray-700">Yêu cầu câu hỏi bảo mật</label>
                    </div>

                    <!-- Min Security Answers -->
                    <div>
                        <label for="security_questions_min_answers" class="block text-sm font-medium text-gray-700">
                            Câu hỏi bắt buộc tối thiểu <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="security_questions_min_answers" name="security_questions_min_answers" min="1" max="5" value="{{ $config->security_questions_min_answers ?? 2 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Device Verification -->
                    <div class="flex items-center">
                        <input type="hidden" name="require_device_verification" value="0">
                        <input type="checkbox" id="require_device_verification" name="require_device_verification" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->require_device_verification)>
                        <label for="require_device_verification" class="ml-2 block text-sm text-gray-700">Xác thực thiết bị mới</label>
                    </div>

                    <!-- Max Concurrent Devices -->
                    <div>
                        <label for="max_concurrent_devices" class="block text-sm font-medium text-gray-700">
                            Số thiết bị đồng thời tối đa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="max_concurrent_devices" name="max_concurrent_devices" min="1" max="10" value="{{ $config->max_concurrent_devices ?? 3 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </fieldset>

            <!-- Login Attempt Settings -->
            <fieldset class="mb-8 pb-8 border-b">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    Cài đặt đăng nhập
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Max Login Attempts -->
                    <div>
                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">
                            Số lần đăng nhập tối đa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="max_login_attempts" name="max_login_attempts" min="3" max="20" value="{{ $config->max_login_attempts ?? 5 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Lockout Duration -->
                    <div>
                        <label for="login_attempt_lockout_minutes" class="block text-sm font-medium text-gray-700">
                            Thời gian khóa (phút) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="login_attempt_lockout_minutes" name="login_attempt_lockout_minutes" min="5" max="1440" value="{{ $config->login_attempt_lockout_minutes ?? 15 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Allow Concurrent Sessions -->
                    <div class="flex items-center">
                        <input type="hidden" name="allow_concurrent_sessions" value="0">
                        <input type="checkbox" id="allow_concurrent_sessions" name="allow_concurrent_sessions" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->allow_concurrent_sessions)>
                        <label for="allow_concurrent_sessions" class="ml-2 block text-sm text-gray-700">Cho phép nhiều phiên</label>
                    </div>

                    <!-- Max Concurrent Sessions -->
                    <div>
                        <label for="max_concurrent_sessions" class="block text-sm font-medium text-gray-700">
                            Phiên đồng thời tối đa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="max_concurrent_sessions" name="max_concurrent_sessions" min="1" max="10" value="{{ $config->max_concurrent_sessions ?? 3 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Session Timeout -->
                    <div>
                        <label for="session_timeout_minutes" class="block text-sm font-medium text-gray-700">
                            Timeout phiên (phút) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="session_timeout_minutes" name="session_timeout_minutes" min="5" max="1440" value="{{ $config->session_timeout_minutes ?? 120 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Idle Timeout -->
                    <div>
                        <label for="idle_timeout_minutes" class="block text-sm font-medium text-gray-700">
                            Timeout không hoạt động (phút) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="idle_timeout_minutes" name="idle_timeout_minutes" min="5" max="1440" value="{{ $config->idle_timeout_minutes ?? 30 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </fieldset>

            <!-- Anomaly Detection Settings -->
            <fieldset class="mb-8 pb-8 border-b">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m0 4v2M9 3h6a9 9 0 019 9v6a9 9 0 01-9 9H9a9 9 0 01-9-9v-6a9 9 0 019-9z"></path>
                    </svg>
                    Phát hiện bất thường
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Anomaly Detection Enabled -->
                    <div class="flex items-center">
                        <input type="hidden" name="anomaly_detection_enabled" value="0">
                        <input type="checkbox" id="anomaly_detection_enabled" name="anomaly_detection_enabled" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->anomaly_detection_enabled)>
                        <label for="anomaly_detection_enabled" class="ml-2 block text-sm text-gray-700">Bật phát hiện bất thường</label>
                    </div>

                    <!-- Anomaly Threshold -->
                    <div>
                        <label for="anomaly_detection_threshold" class="block text-sm font-medium text-gray-700">
                            Ngưỡng phát hiện (0-100) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="anomaly_detection_threshold" name="anomaly_detection_threshold" min="0" max="100" value="{{ $config->anomaly_detection_threshold ?? 50 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Auto Lockout Critical -->
                    <div class="flex items-center">
                        <input type="hidden" name="auto_lockout_critical" value="0">
                        <input type="checkbox" id="auto_lockout_critical" name="auto_lockout_critical" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->auto_lockout_critical)>
                        <label for="auto_lockout_critical" class="ml-2 block text-sm text-gray-700">Tự động khóa khi nguy cấp</label>
                    </div>

                    <!-- Suspicious Activity Threshold -->
                    <div>
                        <label for="suspicious_activity_threshold" class="block text-sm font-medium text-gray-700">
                            Ngưỡng hoạt động đáng ngờ <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="suspicious_activity_threshold" name="suspicious_activity_threshold" min="0" max="100" value="{{ $config->suspicious_activity_threshold ?? 70 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </fieldset>

            <!-- IP Blocking Settings -->
            <fieldset class="mb-8 pb-8 border-b">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Chặn IP
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Enable IP Blocking -->
                    <div class="flex items-center">
                        <input type="hidden" name="enable_ip_blocking" value="0">
                        <input type="checkbox" id="enable_ip_blocking" name="enable_ip_blocking" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->enable_ip_blocking)>
                        <label for="enable_ip_blocking" class="ml-2 block text-sm text-gray-700">Bật chặn IP tự động</label>
                    </div>

                    <!-- Block After Failed Attempts -->
                    <div>
                        <label for="block_ips_after_failed_attempts" class="block text-sm font-medium text-gray-700">
                            Chặn sau số lần thất bại <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="block_ips_after_failed_attempts" name="block_ips_after_failed_attempts" min="3" max="50" value="{{ $config->block_ips_after_failed_attempts ?? 5 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Block Duration -->
                    <div>
                        <label for="ip_block_duration_minutes" class="block text-sm font-medium text-gray-700">
                            Thời gian chặn (phút) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="ip_block_duration_minutes" name="ip_block_duration_minutes" min="5" max="1440" value="{{ $config->ip_block_duration_minutes ?? 60 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Enable Geo Restrictions -->
                    <div class="flex items-center">
                        <input type="hidden" name="enable_geo_restrictions" value="0">
                        <input type="checkbox" id="enable_geo_restrictions" name="enable_geo_restrictions" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->enable_geo_restrictions)>
                        <label for="enable_geo_restrictions" class="ml-2 block text-sm text-gray-700">Hạn chế địa lý</label>
                    </div>

                    <!-- Require New Location Confirmation -->
                    <div class="flex items-center">
                        <input type="hidden" name="require_new_location_confirmation" value="0">
                        <input type="checkbox" id="require_new_location_confirmation" name="require_new_location_confirmation" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->require_new_location_confirmation)>
                        <label for="require_new_location_confirmation" class="ml-2 block text-sm text-gray-700">Xác nhận vị trí mới</label>
                    </div>
                </div>
            </fieldset>

            <!-- Notification Settings -->
            <fieldset class="mb-8 pb-8 border-b">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    Thông báo
                </legend>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="notify_on_new_ip" value="0">
                        <input type="checkbox" id="notify_on_new_ip" name="notify_on_new_ip" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->notify_on_new_ip)>
                        <label for="notify_on_new_ip" class="ml-2 block text-sm text-gray-700">Thông báo IP mới</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="notify_on_new_device" value="0">
                        <input type="checkbox" id="notify_on_new_device" name="notify_on_new_device" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->notify_on_new_device)>
                        <label for="notify_on_new_device" class="ml-2 block text-sm text-gray-700">Thông báo thiết bị mới</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="notify_on_failed_attempts" value="0">
                        <input type="checkbox" id="notify_on_failed_attempts" name="notify_on_failed_attempts" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->notify_on_failed_attempts)>
                        <label for="notify_on_failed_attempts" class="ml-2 block text-sm text-gray-700">Thông báo đăng nhập thất bại</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="notify_on_account_lockout" value="0">
                        <input type="checkbox" id="notify_on_account_lockout" name="notify_on_account_lockout" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->notify_on_account_lockout)>
                        <label for="notify_on_account_lockout" class="ml-2 block text-sm text-gray-700">Thông báo tài khoản bị khóa</label>
                    </div>
                </div>
            </fieldset>

            <!-- Data Retention Settings -->
            <fieldset class="mb-8 pb-8 border-b">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Bảo vệ dữ liệu
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Log All Activities -->
                    <div class="flex items-center">
                        <input type="hidden" name="log_all_activities" value="0">
                        <input type="checkbox" id="log_all_activities" name="log_all_activities" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->log_all_activities)>
                        <label for="log_all_activities" class="ml-2 block text-sm text-gray-700">Ghi log tất cả hoạt động</label>
                    </div>

                    <!-- Data Retention Days -->
                    <div>
                        <label for="data_retention_days" class="block text-sm font-medium text-gray-700">
                            Lưu dữ liệu tối đa (ngày) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="data_retention_days" name="data_retention_days" min="7" max="3650" value="{{ $config->data_retention_days ?? 90 }}" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Strong Password Required -->
                    <div class="flex items-center">
                        <input type="hidden" name="require_strong_password" value="0">
                        <input type="checkbox" id="require_strong_password" name="require_strong_password" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->require_strong_password)>
                        <label for="require_strong_password" class="ml-2 block text-sm text-gray-700">Yêu cầu mật khẩu mạnh</label>
                    </div>
                </div>
            </fieldset>

            <!-- Advanced Settings -->
            <fieldset class="mb-8">
                <legend class="text-xl font-semibold text-gray-900 mb-6">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-5.011 1.318l-.612.612a6 6 0 01-8.496-8.496l.612-.612A6 6 0 006.904 9.172l.477-2.387a2 2 0 00-.547-1.022m15.428 2.428a2 2 0 00-.547-1.022m0 15.428l-2.387-.477a6 6 0 00-5.011 1.318l-.612.612a6 6 0 01-8.496-8.496l.612-.612A6 6 0 006.904 9.172l.477-2.387a2 2 0 00-.547-1.022m0 0a2 2 0 00-1.022-.547m0 0l-2.387.477a6 6 0 00-1.318 5.011l.612.612a6 6 0 018.496 8.496l-.612.612a6 6 0 01-5.011 1.318l-2.387.477a2 2 0 00-.547 1.022"></path>
                    </svg>
                    Cài đặt nâng cao
                </legend>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="enable_biometric_authentication" value="0">
                        <input type="checkbox" id="enable_biometric_authentication" name="enable_biometric_authentication" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->enable_biometric_authentication)>
                        <label for="enable_biometric_authentication" class="ml-2 block text-sm text-gray-700">Bật xác thực sinh học</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="enable_hardware_key_support" value="0">
                        <input type="checkbox" id="enable_hardware_key_support" name="enable_hardware_key_support" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->enable_hardware_key_support)>
                        <label for="enable_hardware_key_support" class="ml-2 block text-sm text-gray-700">Hỗ trợ khóa phần cứng</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="enable_security_config" value="0">
                        <input type="checkbox" id="enable_security_config" name="enable_security_config" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked($config->enable_security_config)>
                        <label for="enable_security_config" class="ml-2 block text-sm text-gray-700">
                            <span class="font-semibold">Kích hoạt cấu hình này</span>
                            <span class="block text-xs text-gray-500 mt-1">Cấu hình này sẽ được sử dụng cho tất cả người dùng</span>
                        </label>
                    </div>
                </div>
            </fieldset>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-between">
                <a href="{{ route('admin.security-settings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Hủy
                </a>

                <div class="space-x-4">
                    <button type="reset" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>

                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Lưu cài đặt
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
