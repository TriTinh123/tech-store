@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('admin.sessions.index') }}" class="text-blue-600 hover:text-blue-700 font-medium mb-4">
                ← Quay lại
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết Phiên Làm Việc</h1>
        </div>

        <!-- User & Session Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- User Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin Người dùng</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Tên</p>
                        <p class="font-medium text-gray-900">{{ $session->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium text-gray-900">{{ $session->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Vai trò</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($session->user->role ?? 'User') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Đăng ký lúc</p>
                        <p class="font-medium text-gray-900">{{ $session->user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Session Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Trạng thái Phiên</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Trạng thái</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $session->logged_out_at ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}
                        ">
                            {{ $session->logged_out_at ? '✗ Đã đăng xuất' : '✓ Hoạt động' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Đăng nhập lúc</p>
                        <p class="font-medium text-gray-900">{{ $session->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Hoạt động cuối</p>
                        <p class="font-medium text-gray-900">{{ $session->last_activity_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @if ($session->logged_out_at)
                        <div>
                            <p class="text-sm text-gray-600">Đăng xuất lúc</p>
                            <p class="font-medium text-gray-900">{{ $session->logged_out_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Device & Location Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin Thiết bị & Vị trí</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Thiết bị</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Tên</p>
                            <p class="font-medium text-gray-900">{{ $session->device_name ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Loại</p>
                            <p class="font-medium text-gray-900">{{ ucfirst($session->device_type) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Trình duyệt</p>
                            <p class="font-medium text-gray-900">{{ $session->browser ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Hệ điều hành</p>
                            <p class="font-medium text-gray-900">{{ $session->os ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">User Agent</p>
                            <p class="font-mono text-xs text-gray-600 break-all">{{ request()->userAgent() }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Vị trí</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Địa chỉ IP</p>
                            <p class="font-mono text-gray-900">{{ $session->ip_address }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Nơi</p>
                            <p class="font-medium text-gray-900">{{ $session->location ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Tọa độ</p>
                            <p class="font-medium text-gray-900">
                                @if ($session->latitude && $session->longitude)
                                    {{ number_format($session->latitude, 4) }}, {{ number_format($session->longitude, 4) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Flags -->
        @if ($session->is_flagged)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-red-900 mb-4">⚠️ Cảnh báo Bảo mật</h2>
                <p class="text-red-700">{{ $session->flag_reason ?? 'Phiên được đánh dấu là đáng ngờ' }}</p>
            </div>
        @endif

        <!-- Activity Log -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Nhật ký Hoạt động</h2>
            <div class="space-y-3">
                @forelse ($session->activities->take(20) as $activity)
                    <div class="flex items-start space-x-3 pb-3 border-b border-gray-200 last:border-0">
                        <span class="text-lg mt-1">
                            @if ($activity->activity_type === 'login') 🔓
                            @elseif ($activity->activity_type === 'logout') 🔒
                            @elseif ($activity->activity_type === 'page_view') 👁️
                            @elseif ($activity->activity_type === 'api_call') 📡
                            @else ⚠️
                            @endif
                        </span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $activity->getActivityTypeLabel() }}</p>
                            <p class="text-sm text-gray-600">{{ $activity->created_at->format('d/m/Y H:i:s') }}</p>
                            @if ($activity->metadata)
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $activity->metadata['path'] ?? '' }}
                                    {{ isset($activity->metadata['method']) ? '(' . $activity->metadata['method'] . ')' : '' }}
                                </p>
                            @endif
                        </div>
                        @if ($activity->is_suspicious)
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Đáng ngờ</span>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-600">Không có hoạt động nào</p>
                @endforelse
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Hành động</h2>
            <div class="flex flex-wrap gap-3">
                @if (!$session->logged_out_at)
                    <form method="POST" action="{{ route('admin.sessions.terminate', $session->id) }}" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn đăng xuất phiên này?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            🚪 Đăng xuất Phiên
                        </button>
                    </form>
                @endif

                @if (!$session->is_flagged)
                    <form method="POST" action="{{ route('admin.sessions.flag', $session->id) }}" class="inline">
                        @csrf
                        <input type="hidden" name="reason" value="Admin flagged this session as suspicious">
                        <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                            ⚠️ Đánh dấu Đáng ngờ
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.sessions.unflag', $session->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            ✓ Bỏ đánh dấu
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('admin.sessions.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Quay lại danh sách phiên
            </a>
        </div>
    </div>
</div>
@endsection
