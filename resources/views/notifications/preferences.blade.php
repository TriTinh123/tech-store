@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:text-blue-700 font-medium mb-4">
                ← Quay lại thông báo
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tùy chọn Thông báo</h1>
            <p class="mt-2 text-gray-600">Tùy chỉnh cách và khi nào bạn nhận được cảnh báo bảo mật</p>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <div class="flex space-x-8">
                <button class="px-4 py-3 border-b-2 border-blue-600 font-medium text-blue-600 text-sm" 
                        onclick="showTab('channels')">
                    📢 Kênh Thông báo
                </button>
                <button class="px-4 py-3 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 text-sm" 
                        onclick="showTab('events')">
                    🔔 Sự kiện
                </button>
                <button class="px-4 py-3 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 text-sm" 
                        onclick="showTab('advanced')">
                    ⚙️ Nâng cao
                </button>
            </div>
        </div>

        <!-- Notification Channels Tab -->
        <div id="channels-tab" class="space-y-6">
            <!-- Email Notifications -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📧 Thông báo Email</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Bật thông báo email</p>
                            <p class="text-sm text-gray-600">Nhận cảnh báo bảo mật qua email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ $preferences->email_enabled ? 'checked' : '' }} 
                                   onchange="toggleEmailSection()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    @if($preferences->email_enabled)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-4">
                            <div>
                                <label for="notification_email" class="block text-sm font-medium text-gray-900 mb-1">
                                    Địa chỉ Email
                                </label>
                                <input type="email" id="notification_email" name="notification_email" 
                                       value="{{ $preferences->notification_email ?? auth()->user()->email }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       form="email-form">
                            </div>

                            <div>
                                <label for="email_frequency" class="block text-sm font-medium text-gray-900 mb-1">
                                    Tần suất
                                </label>
                                <select id="email_frequency" name="email_frequency" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        form="preferences-form">
                                    <option value="immediate" {{ $preferences->email_frequency === 'immediate' ? 'selected' : '' }}>
                                        Ngay lập tức
                                    </option>
                                    <option value="daily" {{ $preferences->email_frequency === 'daily' ? 'selected' : '' }}>
                                        Hàng ngày
                                    </option>
                                    <option value="weekly" {{ $preferences->email_frequency === 'weekly' ? 'selected' : '' }}>
                                        Hàng tuần
                                    </option>
                                </select>
                            </div>

                            <div class="pt-3 border-t border-blue-200">
                                <p class="text-sm text-blue-700">
                                    ✓ Email xác minh: {{ $preferences->email_verified ? '✓ Đã xác minh' : '⚠️ Chưa xác minh' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SMS Notifications -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📱 Thông báo SMS</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Bật thông báo SMS</p>
                            <p class="text-sm text-gray-600">Nhận cảnh báo khẩn cấp qua SMS</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ $preferences->sms_enabled ? 'checked' : '' }}
                                   onchange="toggleSmsSection()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    @if($preferences->sms_enabled && $preferences->phone_verified)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-700">
                                ✓ Số điện thoại xác minh: {{ $preferences->phone_number }}
                            </p>
                        </div>
                    @elseif($preferences->sms_enabled)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 space-y-4">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-900 mb-1">
                                    Số điện thoại
                                </label>
                                <input type="tel" id="phone_number" name="phone_number" 
                                       placeholder="+84 xxx xxx xxxx"
                                       value="{{ $preferences->phone_number ?? '' }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       form="sms-form">
                            </div>
                            <button type="submit" form="sms-form" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                Xác minh Số điện thoại
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- In-App Notifications -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔔 Thông báo Trên Website</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Bật thông báo trên website</p>
                            <p class="text-sm text-gray-600">Nhận cảnh báo popup/toast trên website</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="in_app_enabled" class="sr-only peer" 
                                   {{ $preferences->in_app_enabled ? 'checked' : '' }}
                                   form="preferences-form">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Tab -->
        <div id="events-tab" class="hidden space-y-4">
            <form method="POST" action="{{ route('notifications.preferences.update') }}" id="preferences-form">
                @csrf
                
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-medium text-gray-900">⚠️ Đăng nhập đồng thời</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi có đăng nhập từ hai địa điểm cùng lúc</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_concurrent_login" class="sr-only peer" 
                                   {{ $preferences->notify_concurrent_login ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-medium text-gray-900">🚨 Hoạt động đáng ngờ</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi phát hiện hoạt động bất thường</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_suspicious_activity" class="sr-only peer" 
                                   {{ $preferences->notify_suspicious_activity ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-medium text-gray-900">🔐 Thay đổi 3FA</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi thay đổi cài đặt xác thực ba yếu tố</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_3fa_changes" class="sr-only peer" 
                                   {{ $preferences->notify_3fa_changes ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-medium text-gray-900">🚫 IP bị chặn</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi IP của bạn bị chặn</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_ip_blocked" class="sr-only peer" 
                                   {{ $preferences->notify_ip_blocked ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-medium text-gray-900">🔑 Thay đổi mật khẩu</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi thay đổi mật khẩu</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_password_change" class="sr-only peer" 
                                   {{ $preferences->notify_password_change ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-medium text-gray-900">📱 Thiết bị mới</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi đăng nhập từ thiết bị mới</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_new_device" class="sr-only peer" 
                                   {{ $preferences->notify_new_device ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3">
                        <div>
                            <h3 class="font-medium text-gray-900">📍 Thay đổi vị trí</h3>
                            <p class="text-sm text-gray-600">Cảnh báo khi đăng nhập từ vị trí mới</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_location_change" class="sr-only peer" 
                                   {{ $preferences->notify_location_change ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="mt-6 w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Lưu Tùy chọn Sự kiện
                </button>
            </form>
        </div>

        <!-- Advanced Tab -->
        <div id="advanced-tab" class="hidden space-y-6">
            <form method="POST" action="{{ route('notifications.preferences.update') }}" id="preferences-form">
                @csrf

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">⏰ Giờ yên tĩnh</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Bật giờ yên tĩnh</p>
                                <p class="text-sm text-gray-600">Không nhận thông báo trong khung giờ này</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="quiet_hours_enabled" class="sr-only peer" 
                                       {{ $preferences->quiet_hours_enabled ? 'checked' : '' }}
                                       onchange="toggleQuietHours()">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        @if($preferences->quiet_hours_enabled)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="quiet_hours_start" class="block text-sm font-medium text-gray-900 mb-1">
                                        Bắt đầu
                                    </label>
                                    <input type="time" id="quiet_hours_start" name="quiet_hours_start" 
                                           value="{{ $preferences->quiet_hours_start?->format('H:i') ?? '22:00' }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="quiet_hours_end" class="block text-sm font-medium text-gray-900 mb-1">
                                        Kết thúc
                                    </label>
                                    <input type="time" id="quiet_hours_end" name="quiet_hours_end" 
                                           value="{{ $preferences->quiet_hours_end?->format('H:i') ?? '08:00' }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <button type="submit" class="w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Lưu Tùy chọn Nâng cao
                </button>
            </form>
        </div>

        <!-- Back Link -->
        <div class="mt-8">
            <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Quay lại hồ sơ
            </a>
        </div>
    </div>
</div>

<!-- Forms -->
<form id="email-form" method="POST" action="{{ route('notifications.preferences.update-email') }}" class="hidden">
    @csrf
    <input type="email" name="notification_email">
</form>

<form id="sms-form" method="POST" action="{{ route('notifications.preferences.setup-sms') }}" class="hidden">
    @csrf
    <input type="tel" name="phone_number">
</form>

<script>
function showTab(tab) {
    // Hide all tabs
    document.getElementById('channels-tab').classList.add('hidden');
    document.getElementById('events-tab').classList.add('hidden');
    document.getElementById('advanced-tab').classList.add('hidden');

    // Show selected tab
    document.getElementById(tab + '-tab').classList.remove('hidden');

    // Update button styles
    document.querySelectorAll('button').forEach(btn => {
        if (btn.textContent.includes('📢') || btn.textContent.includes('🔔') || btn.textContent.includes('⚙️')) {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-600');
        }
    });

    // Highlight active button
    event.target.classList.remove('border-transparent', 'text-gray-600');
    event.target.classList.add('border-blue-600', 'text-blue-600');
}

function toggleEmailSection() {
    document.querySelector('[name="email_enabled"]').value = event.target.checked;
}

function toggleSmsSection() {
    document.querySelector('[name="sms_enabled"]').value = event.target.checked;
}

function toggleQuietHours() {
    const start = document.getElementById('quiet_hours_start');
    const end = document.getElementById('quiet_hours_end');
    if (event.target.checked) {
        start.disabled = false;
        end.disabled = false;
    } else {
        start.disabled = true;
        end.disabled = true;
    }
}
</script>
@endsection
