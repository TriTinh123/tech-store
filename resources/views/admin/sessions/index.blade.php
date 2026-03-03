@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Giám sát Phiên Làm Việc</h1>
            <p class="mt-2 text-gray-600">Quản lý toàn bộ phiên làm việc từ tất cả người dùng</p>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Phiên Hoạt Động</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_active_sessions'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Cảnh báo Đăng Nhập Đồng Thời</p>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['concurrent_logins_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Thiết Bị Độc Nhất</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['unique_devices'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Địa Điểm Độc Nhất</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['unique_locations'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <div class="flex space-x-8">
                <button
                    class="px-4 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'sessions' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}"
                    onclick="document.getElementById('sessions-tab').classList.remove('hidden'); document.getElementById('concurrent-tab').classList.add('hidden');"
                >
                    Phiên Làm Việc
                </button>
                <button
                    class="px-4 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'concurrent' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}"
                    onclick="document.getElementById('concurrent-tab').classList.remove('hidden'); document.getElementById('sessions-tab').classList.add('hidden');"
                >
                    Cảnh báo Đăng Nhập Đồng Thời ({{ $concurrentLogins->count() }})
                </button>
            </div>
        </div>

        <!-- Sessions Tab -->
        <div id="sessions-tab" class="{{ $activeTab !== 'sessions' ? 'hidden' : '' }}">
            <!-- Search & Filter -->
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input
                            type="text"
                            placeholder="Tìm kiếm người dùng..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            id="session-search"
                        >
                    </div>
                    <div>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tất cả loại thiết bị</option>
                            <option value="mobile">📱 Điện thoại</option>
                            <option value="tablet">📱 Máy tính bảng</option>
                            <option value="desktop">🖥️ Máy tính để bàn</option>
                        </select>
                    </div>
                    <div>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">✓ Hoạt động</option>
                            <option value="flagged">⚠️ Đáng ngờ</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sessions Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Người dùng</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Thiết bị</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Địa chỉ IP</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vị trí</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Hoạt động cuối</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sessions as $session)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $session->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $session->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $session->getDeviceInfo() }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($session->device_type) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $session->ip_address }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $session->location ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $session->getTimeSinceLastActivity() }}</td>
                                <td class="px-6 py-4">
                                    @if ($session->is_flagged)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ⚠️ Đáng ngờ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Bình thường
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a
                                        href="{{ route('admin.sessions.show', $session->id) }}"
                                        class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                                    >
                                        Xem
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('admin.sessions.terminate', $session->id) }}"
                                        class="inline"
                                        onsubmit="return confirm('Bạn chắc chắn muốn đăng xuất phiên này?');"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
                                        >
                                            Đăng xuất
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-600">
                                    Không có phiên hoạt động nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($sessions->hasPages())
                <div class="mt-6">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>

        <!-- Concurrent Logins Tab -->
        <div id="concurrent-tab" class="{{ $activeTab !== 'concurrent' ? 'hidden' : '' }}">
            <div class="space-y-4">
                @forelse ($concurrentLogins as $login)
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-600">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $login->user->name }}
                                </h3>
                                <p class="text-sm text-gray-600">{{ $login->user->email }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if ($login->status === 'detected') bg-yellow-100 text-yellow-800
                                @elseif ($login->status === 'confirmed') bg-red-100 text-red-800
                                @elseif ($login->status === 'authorized') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($login->status) }}
                            </span>
                        </div>

                        <!-- Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <!-- Session 1 -->
                            <div class="bg-gray-50 rounded p-4">
                                <p class="font-semibold text-gray-900 mb-2">📍 Phiên 1 (Cũ)</p>
                                <div class="text-sm space-y-1 text-gray-600">
                                    <p><span class="font-medium">IP:</span> <span class="font-mono">{{ $login->first_session_ip }}</span></p>
                                    <p><span class="font-medium">Vị trí:</span> {{ $login->first_session_location ?? '-' }}</p>
                                    <p><span class="font-medium">Thời gian:</span> {{ $login->first_session_created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <!-- Session 2 -->
                            <div class="bg-gray-50 rounded p-4">
                                <p class="font-semibold text-gray-900 mb-2">📍 Phiên 2 (Mới)</p>
                                <div class="text-sm space-y-1 text-gray-600">
                                    <p><span class="font-medium">IP:</span> <span class="font-mono">{{ $login->second_session_ip }}</span></p>
                                    <p><span class="font-medium">Vị trí:</span> {{ $login->second_session_location ?? '-' }}</p>
                                    <p><span class="font-medium">Thời gian:</span> {{ $login->second_session_created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Suspicious Indicators -->
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                            <p class="text-sm font-medium text-red-900 mb-2">🚨 Dấu hiệu đáng ngờ:</p>
                            <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                @if (!$login->isSameLocation())
                                    <li>Địa chỉ IP khác nhau ({{ $login->time_difference }} giây)</li>
                                @endif
                                @if ($login->isSuspiciousTimeDifference())
                                    <li>Đăng nhập trong thời gian quá ngắn</li>
                                @endif
                            </ul>
                        </div>

                        <!-- Admin Notes -->
                        @if ($login->admin_notes)
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                                <p class="text-sm"><span class="font-medium text-blue-900">Ghi chú:</span> <span class="text-blue-700">{{ $login->admin_notes }}</span></p>
                            </div>
                        @endif

                        <!-- Actions -->
                        @if ($login->status === 'detected')
                            <div class="flex gap-3">
                                <form method="POST" action="{{ route('admin.concurrent-logins.confirm', $login->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                        ✓ Xác nhận Đe Dọa
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.concurrent-logins.authorize', $login->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                        ✓ Cho phép
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.concurrent-logins.false-positive', $login->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                                        ✗ Cảnh báo Giả
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-3 bg-gray-50 rounded">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Xử lý hôm:</span> {{ $login->confirmed_at?->format('d/m/Y H:i') ?? '-' }}
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <p class="text-gray-600">✓ Không có cảnh báo đăng nhập đồng thời</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Back Link -->
        <div class="mt-8">
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Quay lại dashboard
            </a>
        </div>
    </div>
</div>
@endsection
