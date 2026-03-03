@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Trung tâm Thông báo</h1>
            <p class="mt-2 text-gray-600">Quản lý và cải thiện tùy chọn thông báo bảo mật của bạn</p>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Chưa đọc</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['unread_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Cảnh báo Khẩn cấp</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['critical_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Tổng Thông báo</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_notifications'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Không thành công</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['failed_notifications'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('notifications.preferences') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ⚙️ Tùy chọn Thông báo
            </a>
            @if($stats['unread_count'] > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-green-600 hover:text-green-700 font-medium">
                        ✓ Đánh dấu tất cả là đã đọc
                    </button>
                </form>
            @endif
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Tất cả loại</option>
                        <option value="concurrent_login" {{ $filterType === 'concurrent_login' ? 'selected' : '' }}>Đăng nhập đồng thời</option>
                        <option value="suspicious_activity" {{ $filterType === 'suspicious_activity' ? 'selected' : '' }}>Hoạt động đáng ngờ</option>
                        <option value="ip_blocked" {{ $filterType === 'ip_blocked' ? 'selected' : '' }}>IP bị chặn</option>
                        <option value="3fa_changes" {{ $filterType === '3fa_changes' ? 'selected' : '' }}>3FA thay đổi</option>
                        <option value="new_device" {{ $filterType === 'new_device' ? 'selected' : '' }}>Thiết bị mới</option>
                        <option value="location_change" {{ $filterType === 'location_change' ? 'selected' : '' }}>Thay đổi vị trí</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="unread" {{ $filterStatus === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                        <option value="read" {{ $filterStatus === 'read' ? 'selected' : '' }}>Đã đọc</option>
                    </select>
                </div>
                <div>
                    <select name="severity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Tất cả mức độ</option>
                        <option value="critical" {{ $filterSeverity === 'critical' ? 'selected' : '' }}>Khẩn cấp</option>
                        <option value="warning" {{ $filterSeverity === 'warning' ? 'selected' : '' }}>Cảnh báo</option>
                        <option value="info" {{ $filterSeverity === 'info' ? 'selected' : '' }}>Thông tin</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Notifications List -->
        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-{{ $notification->getSeverityColor() }}-500 hover:shadow-md transition-shadow {{ !$notification->read ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-start space-x-3 flex-1">
                            <span class="text-2xl">{{ $notification->getSeverityIcon() }}</span>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                @if($notification->details)
                                    <div class="text-xs text-gray-500 mt-2">
                                        @foreach($notification->details as $key => $value)
                                            <span class="mr-3">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            @if(!$notification->read)
                                <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Chưa đọc</span>
                            @endif
                        </div>
                    </div>

                    <!-- Details -->
                    @if($notification->details)
                        <div class="bg-gray-50 rounded p-3 my-3 text-sm">
                            @foreach($notification->details as $key => $value)
                                <p class="text-gray-700"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ is_array($value) ? implode(', ', $value) : $value }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex gap-3 mt-3">
                        @if($notification->action_url && $notification->action_label)
                            <a href="{{ $notification->action_url }}" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">
                                {{ $notification->action_label }}
                            </a>
                        @endif

                        @if(!$notification->read)
                            <form method="POST" action="{{ route('notifications.mark-as-read', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm">
                                    ✓ Đánh dấu đã đọc
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifications.mark-as-unread', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                                    Đánh dấu chưa đọc
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm">
                                🗑️ Xóa
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600 text-lg">✓ Không có thông báo</p>
                    <p class="text-gray-500 mt-2">Tất cả các cảnh báo bảo mật đều đã được xử lý</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif

        <!-- Back Link -->
        <div class="mt-8">
            <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Quay lại hồ sơ
            </a>
        </div>
    </div>
</div>
@endsection
