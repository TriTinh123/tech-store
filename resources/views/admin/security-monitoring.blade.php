@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bảng Điều Khiển An Ninh</h1>
                    <p class="mt-2 text-gray-600">Giám sát tất cả sự kiện an ninh và hoạt động bất thường</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Cập nhật lần cuối: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Key Metrics Row 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Tổng Người Dùng</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalUsers }}</p>
                    </div>
                    <svg class="h-12 w-12 text-blue-100" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM9 10a3 3 0 11-6 0 3 3 0 016 0zm12-2a3 3 0 11-6 0 3 3 0 016 0zM9 20a3 3 0 11-6 0 3 3 0 016 0zm6-4a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Phiên Hoạt Động</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeSessions }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $uniqueIps }} IP riêng</p>
                    </div>
                    <svg class="h-12 w-12 text-green-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <!-- Suspicious Logins -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Đăng Nhập Bất Thường</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $suspiciousCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $highRiskCount }} cao/crítica</p>
                    </div>
                    <svg class="h-12 w-12 text-orange-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <!-- Tài Khoản Khóa -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Tài Khoản Bị Khóa</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $lockedAccounts }}</p>
                        <p class="text-xs text-gray-500 mt-1">15 phút khóa</p>
                    </div>
                    <svg class="h-12 w-12 text-red-100" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Key Metrics Row 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Security Alerts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Cảnh Báo An Ninh</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalAlerts }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $unreadAlerts }} chưa đọc</p>
                    </div>
                    <svg class="h-12 w-12 text-purple-100" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"/>
                    </svg>
                </div>
            </div>

            <!-- Failed Attempts (24h) -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Đăng Nhập Thất Bại (24h)</p>
                        <p class="text-3xl font-bold text-red-500 mt-2">{{ $recentFailedAttempts }}</p>
                        <p class="text-xs text-gray-500 mt-1">Thành công: {{ $recentSuccessfulAttempts }}</p>
                    </div>
                    <svg class="h-12 w-12 text-red-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.36a6 6 0 018.367 8.529l7.905 7.905a1 1 0 01-1.414 1.414l-7.905-7.905z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <!-- Critical Alerts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Cảnh Báo Crítica</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $criticalAlerts }}</p>
                        <p class="text-xs text-gray-500 mt-1">Cần chú ý ngay lập tức</p>
                    </div>
                    <svg class="h-12 w-12 text-red-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18.355 18.998H1.645A.5.5 0 011.144 18.06l8.355-16.12a.5.5 0 01.901 0l8.354 16.12a.5.5 0 01-.444.938z"/>
                    </svg>
                </div>
            </div>

            <!-- Risk Level -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Mức Rủi Ro Cao/Crítica</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $criticalRiskCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">Yêu cầu kiểm tra</p>
                    </div>
                    <svg class="h-12 w-12 text-orange-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13 10V3L4 14h7v7l9-11h-7z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Recent Security Events -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Sự Kiện An Ninh Gần Đây</h2>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                        @forelse ($recentAlerts as $alert)
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $severityColor = match($alert->severity) {
                                                    'critical' => 'bg-red-100 text-red-800',
                                                    'high' => 'bg-orange-100 text-orange-800',
                                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                                    'low' => 'bg-blue-100 text-blue-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $severityColor }}">{{ ucfirst($alert->severity) }}</span>
                                            <p class="text-sm font-medium text-gray-900">{{ $alert->user->name ?? 'Unknown' }}</p>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $alert->getAlertTypeLabel() }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if (!$alert->isRead())
                                        <span class="inline-block h-2 w-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500">
                                <p>Không có cảnh báo gần đây</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Suspicious Logins -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Đăng Nhập Bất Thường Gần Đây</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Người Dùng</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">IP</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Vị Trí</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Thiết Bị</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Mức Độ</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Lý Do</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($suspiciousLogins as $login)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-medium text-gray-900">{{ $login->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-3 font-mono text-xs text-gray-600">{{ $login->ip_address }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $login->city }}, {{ $login->country }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $login->device_type }} - {{ $login->browser }}</td>
                                        <td class="px-6 py-3">
                                            @php
                                                $riskColor = match($login->risk_level) {
                                                    'critical' => 'bg-red-100 text-red-800',
                                                    'high' => 'bg-orange-100 text-orange-800',
                                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                                    'low' => 'bg-blue-100 text-blue-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $riskColor }}">{{ ucfirst($login->risk_level) }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-gray-600 truncate max-w-xs">{{ $login->reason }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            Không có đăng nhập bất thường
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Locked Accounts -->
                @if ($lockedAccounts > 0)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                            <h2 class="text-lg font-semibold text-red-900">Tài Khoản Bị Khóa ({{ $lockedAccounts }})</h2>
                        </div>
                        <div class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                            @foreach ($lockedUsers as $user)
                                <div class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $user['name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $user['email'] }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">{{ $user['failedAttempts'] }} lần thất bại</span>
                                        <span class="text-xs text-gray-500">Mở khóa: {{ $user['lockedUntil']?->format('H:i') ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Top Risky IPs -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">IP Nguy Hiểm Nhất</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse ($topRiskyIps as $ip)
                            <div class="px-6 py-4">
                                <p class="font-mono text-sm font-medium text-gray-900">{{ $ip->ip_address }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-600">{{ $ip->count }} lần</span>
                                    <span class="text-xs font-semibold px-2 py-1 rounded {{ match($ip->risk_level) {
                                        'critical' => 'bg-red-100 text-red-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    } }}">{{ ucfirst($ip->risk_level) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                Không có dữ liệu
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Risky Locations -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Vị Trí Nguy Hiểm Nhất</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse ($topRiskyLocations as $location)
                            <div class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $location->city }}, {{ $location->country }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $location->count }} sự kiện</p>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                Không có dữ liệu
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Suspicious Users -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Người Dùng Đáng Ngờ Nhất</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse ($topSuspiciousUsers as $user)
                            <div class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $user->user->name ?? 'Unknown' }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-600">{{ $user->count }} sự kiện</span>
                                    <a href="mailto:{{ $user->user->email }}" class="text-xs text-blue-600 hover:text-blue-900">Liên hệ</a>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                Không có dữ liệu
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Risk Level Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Phân Bố Mức Rủi Ro</h2>
                <div class="space-y-4">
                    @php
                        $riskLevels = [
                            'critical' => ['label' => 'Crítica', 'color' => 'bg-red-500'],
                            'high' => ['label' => 'Cao', 'color' => 'bg-orange-500'],
                            'medium' => ['label' => 'Trung bình', 'color' => 'bg-yellow-500'],
                            'low' => ['label' => 'Thấp', 'color' => 'bg-blue-500'],
                        ];
                        $totalRisk = $riskLevelBreakdown->sum('count') ?? 1;
                    @endphp
                    @foreach ($riskLevels as $level => $info)
                        @php
                            $count = $riskLevelBreakdown->get($level)?->count ?? 0;
                            $percentage = $totalRisk > 0 ? round(($count / $totalRisk) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $info['label'] }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $info['color'] }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Alert Type Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Loại Cảnh Báo</h2>
                <div class="space-y-3">
                    @php
                        $alertTypes = [
                            'account_locked' => 'Tài khoản khóa',
                            'new_ip' => 'IP mới',
                            'new_device' => 'Thiết bị mới',
                            'unusual_time' => 'Thời gian bất thường',
                            'rapid_location' => 'Vị trí nhanh',
                            'failed_attempt' => 'Đăng nhập thất bại',
                        ];
                    @endphp
                    @foreach ($alertTypes as $type => $label)
                        @php
                            $count = $alertTypeBreakdown->get($type)?->count ?? 0;
                        @endphp
                        @if ($count > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded">{{ $count }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh security dashboard every 30 seconds
    setInterval(function() {
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('vi-VN');
    }, 30000);
</script>
@endsection
